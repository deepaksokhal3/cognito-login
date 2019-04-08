<?php
namespace pmill\AwsCognito;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Exception;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\Converter\StandardConverter;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use pmill\AwsCognito\Exception\ChallengeException;
use pmill\AwsCognito\Exception\CognitoResponseException;
use pmill\AwsCognito\Exception\TokenExpiryException;
use pmill\AwsCognito\Exception\TokenVerificationException;
class CognitoClient
{
    const CHALLENGE_NEW_PASSWORD_REQUIRED = 'NEW_PASSWORD_REQUIRED';

    /**
     * @var string
     */
    protected $appClientId;

    /**
     * @var string
     */
    protected $appClientSecret;

    /**
     * @var CognitoIdentityProviderClient
     */
    protected $client;

    /**
     * @var JWKSet
     */
    protected $jwtWebKeys;

    /**
     * @var JWKSet
     */
    protected $hasKey;

      /**
     * @var JWKSet
     */
    protected $firstKey;

     /**
     * @var JWKSet
     */
    protected $secondKey;


    /**
     * @var string
     */
    protected $region;

    /**
     * @var string
     */
    protected $userPoolId;

    /**
     * CognitoClient constructor.
     *
     * @param CognitoIdentityProviderClient $client
     */
    public function __construct(CognitoIdentityProviderClient $client)
    {
        $this->client = $client;
        $this->hasKey = 'AES-128-CBC';
        $this->firstKey =  'Lk5Uz3slx3BrAghS1aaW5AYgWZRV0tIX5eI0yPchFz4=';
        $this->secondKey =  'EZ44mFi3TlAey1b2w4Y7lVDuqO+SRxGXsa7nctnr/JmMrA2vN6EJhrvdVZbxaQs5jpSe34X3ejFK/o9+Y5c83w==';
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return array
     * @throws ChallengeException
     * @throws Exception
     */
    public function authenticate($username, $password)
    {
        try {
            $response = $this->client->adminInitiateAuth([
                'AuthFlow' => 'ADMIN_NO_SRP_AUTH',
                'AuthParameters' => [
                    'USERNAME' => $username,
                    'PASSWORD' => $password,
                    'SECRET_HASH' => $this->cognitoSecretHash($username),
                ],
                'ClientId' => $this->appClientId,
                'UserPoolId' => $this->userPoolId,
            ]);
            $response =  $this->handleAuthenticateResponse($response->toArray());
            $result = $this->buildFormatedObject($this->getCurrentUser($response['AccessToken']));
            $_SESSION['sub_id'] = isset($result['sub'])? $this->encript($result['sub']):'';
            return $response;
        } catch (CognitoIdentityProviderException $e) {
            return $e->getAwsErrorMessage();
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }

    /**
     * @param string $challengeName
     * @param array $challengeResponses
     * @param string $session
     *
     * @return array
     * @throws ChallengeException
     * @throws Exception
     */
    public function respondToAuthChallenge($challengeName, array $challengeResponses, $session)
    {
        try {
            $response = $this->client->respondToAuthChallenge([
                'ChallengeName' => $challengeName,
                'ChallengeResponses' => $challengeResponses,
                'ClientId' => $this->appClientId,
                'Session' => $session,
            ]);
            
            return $this->handleAuthenticateResponse($response->toArray());
        } catch (CognitoIdentityProviderException $e) {
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }

     /**
     * @param string $string
     * @param string $response
     */
    public function encript($sting){
        $first_key = base64_decode($this->firstKey);
        $second_key = base64_decode($this->secondKey);    
            
        $method = "aes-256-cbc";    
        $iv_length = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($iv_length);
                
        $first_encrypted = openssl_encrypt($sting,$method,$first_key, OPENSSL_RAW_DATA ,$iv);    
        $second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
                    
        return base64_encode($iv.$second_encrypted.$first_encrypted); 
    } 

    /**
     * @param string $string
     * @param string $response
     */
    public function decript($encoded){
        $first_key = base64_decode($this->firstKey);
        $second_key = base64_decode($this->secondKey);            
        $mix = base64_decode($encoded);
                
        $method = "aes-256-cbc";    
        $iv_length = openssl_cipher_iv_length($method);
                    
        $iv = substr($mix,0,$iv_length);
        $second_encrypted = substr($mix,$iv_length,64);
        $first_encrypted = substr($mix,$iv_length+64);
                    
        $data = openssl_decrypt($first_encrypted,$method,$first_key,OPENSSL_RAW_DATA,$iv);
        $second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
            
        if (hash_equals($second_encrypted,$second_encrypted_new))
        return $data;
    }
    
    /**
     * @param string $username
     * @param string $newPassword
     * @param string $session
     * @return array
     * @throws ChallengeException
     * @throws Exception
     */
    public function respondToNewPasswordRequiredChallenge($username, $newPassword, $session)
    {
        return $this->respondToAuthChallenge(
            self::CHALLENGE_NEW_PASSWORD_REQUIRED,
            [
                'NEW_PASSWORD' => $newPassword,
                'USERNAME' => $username,
                'SECRET_HASH' => $this->cognitoSecretHash($username),
            ],
            $session
        );
    }

   /**
     * @param string return
     * @return string
     * @throws Exception
     */
    public function createGroup($name, $description)
    {
        try {
            return $this->client->createGroup([
                'Description' => $description,
                'GroupName' => $name, // REQUIRED
                'UserPoolId' => $this->userPoolId, // REQUIRED
            ]);
        } catch (CognitoIdentityProviderException $e) {
            return $e->getAwsErrorMessage();
        }
    }


    /**
     * @param string $username
     * @param string $refreshToken
     * @return string
     * @throws Exception
     */
    public function refreshAuthentication($username, $refreshToken)
    {
        try {
            $response = $this->client->adminInitiateAuth([
                'AuthFlow' => 'REFRESH_TOKEN_AUTH',
                'AuthParameters' => [
                    'USERNAME' => $username,
                    'REFRESH_TOKEN' => $refreshToken,
                    'SECRET_HASH' => $this->cognitoSecretHash($username),
                ],
                'ClientId' => $this->appClientId,
                'UserPoolId' => $this->userPoolId,
            ])->toArray();

            return $response['AuthenticationResult'];
        } catch (CognitoIdentityProviderException $e) {
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }

    /**
     * @param string $accessToken
     * @param string $previousPassword
     * @param string $proposedPassword
     * @throws Exception
     * @throws TokenExpiryException
     * @throws TokenVerificationException
     */
    public function changePassword($accessToken, $previousPassword, $proposedPassword)
    {
        $this->verifyAccessToken($accessToken);
        try {
           return $this->client->changePassword([
                'AccessToken' => $accessToken,
                'PreviousPassword' => $previousPassword,
                'ProposedPassword' => $proposedPassword,
            ]);

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param string $confirmationCode
     * @param string $username
     * @throws Exception
     */
    public function confirmUserRegistration($confirmationCode, $username)
    {
        try {
            $this->client->confirmSignUp([
                'ClientId' => $this->appClientId,
                'ConfirmationCode' => $confirmationCode,
                'SecretHash' => $this->cognitoSecretHash($username),
                'Username' => $username,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            return $e->getAwsErrorMessage();
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }

     /*
     * @param string $username
     * @return AwsResult
     * @throws UserNotFoundException
     * @throws CognitoResponseException
     */
    public function getCurrentUser($accessToken)
    {
        try {
            return $this->client->getUser([
                'AccessToken' => $accessToken
            ]);
        } catch (Exception $e) {
            return $e->getAwsErrorMessage();
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }

    /*
     * @param string $username
     * @return AwsResult
     * @throws UserNotFoundException
     * @throws CognitoResponseException
     */
    public function getUser($username)
    {
        try {
            return $this->client->adminGetUser([
                'Username' => $username,
                'UserPoolId' => $this->userPoolId,
            ]);
        } catch (Exception $e) {
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }

    /**
     * @param string $accessToken
     * @throws Exception
     * @throws TokenExpiryException
     * @throws TokenVerificationException
     */
    public function deleteUser($username)
    {
        try {
            return $this->client->adminDeleteUser([
                'UserPoolId' => $this->userPoolId, // REQUIRED
                'Username' => $username, // REQUIRED
            ]);
        } catch (CognitoIdentityProviderException $e) {
            return $e->getAwsErrorMessage();
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }

    /**
     * @param string $username
     * @param string $groupName
     * @throws Exception
     */
    public function addUserToGroup($username, $groupName) {
        try {
           return $this->client->adminAddUserToGroup([
                'UserPoolId' => $this->userPoolId,
                'Username' => $username,
                "GroupName" => $groupName
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }

    /**
     * @param $username
     * @param array $attributes
     * @throws Exception
     */
    public function updateUserAttributes($username, array $attributes = [])
    {
        $userAttributes = $this->buildAttributesArray($attributes);

        try {
            return $this->client->adminUpdateUserAttributes([
                'Username' => $username,
                'UserPoolId' => $this->userPoolId,
                'UserAttributes' => $userAttributes,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }

    /**
     * @return JWKSet
     */
    public function getJwtWebKeys()
    {
        if (!$this->jwtWebKeys) {
            $json = $this->downloadJwtWebKeys();
            $this->jwtWebKeys = JWKSet::createFromJson($json);
        }

        return $this->jwtWebKeys;
    }

    /**
     * @param JWKSet $jwtWebKeys
     */
    public function setJwtWebKeys(JWKSet $jwtWebKeys)
    {
        $this->jwtWebKeys = $jwtWebKeys;
    }

    /**
     * @return string
     */
    protected function downloadJwtWebKeys()
    {

        $url = sprintf(
            'https://cognito-idp.%s.amazonaws.com/%s/.well-known/jwks.json',
            $this->region,
            $this->userPoolId
        );

        return file_get_contents($url);
    }

    /**
     * @param string $username
     * @param string $password
     * @param array $attributes
     * @return string
     * @throws Exception
     */
    public function registerUser($username, $password, array $attributes = [])
    {
        $userAttributes = $this->buildAttributesArray($attributes);

        try {
            $response = $this->client->signUp([
                'ClientId' => $this->appClientId,
                'Password' => $password,
                'SecretHash' => $this->cognitoSecretHash($username),
                'UserAttributes' => $userAttributes,
                'Username' => $username,
            ]);

            return $response['UserSub'];
        } catch (CognitoIdentityProviderException $e) {
            $response['error'] = $e->getAwsErrorMessage();
            return $response;
        }
    }

    /**
     * @param string $username
     * @throws Exception
     */

    public function disableUser($username){
        try{
            return $this->client->adminDisableUser([
                'UserPoolId' => $this->userPoolId, // REQUIRED
                'Username' => $username, // REQUIRED
            ]);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @param string $username
     * @throws Exception
     */

    public function enableUser($username){
        try{
            return $this->client->adminEnableUser([
                'UserPoolId' => $this->userPoolId, // REQUIRED
                'Username' => $username, // REQUIRED
            ]);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @param string $confirmationCode
     * @param string $username
     * @param string $proposedPassword
     * @throws Exception
     */
    public function resetPassword($confirmationCode, $username, $proposedPassword)
    {
        try {
            return $this->client->confirmForgotPassword([
                'ClientId' => $this->appClientId,
                'ConfirmationCode' => $confirmationCode,
                'Password' => $proposedPassword,
                'SecretHash' => $this->cognitoSecretHash($username),
                'Username' => $username,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            return $e->getAwsErrorMessage();
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }


     /**
     * @param string $groupName
     * @param string return
     * @throws Exception
     */
    public function getListUsersInGroup($groupName)
    {
        try {
            return $this->client->listUsersInGroup([
                'GroupName' => $groupName, // REQUIRED
                'UserPoolId' => $this->userPoolId, // REQUIRED
            ]);
        } catch (CognitoIdentityProviderException $e) {
            return $e->getAwsErrorMessage();
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }
  

    /**
     * @param string $username
     * @throws Exception
     */
    public function resendRegistrationConfirmationCode($username)
    {
        try {
            return $this->client->resendConfirmationCode([
                'ClientId' => $this->appClientId,
                'SecretHash' => $this->cognitoSecretHash($username),
                'Username' => $username,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }

    /**
     * @param string $username
     * @throws Exception
     */
    public function sendForgottenPasswordRequest($username)
    {
        try {
            return  $this->client->forgotPassword([
                'ClientId' => $this->appClientId,
                'SecretHash' => $this->cognitoSecretHash($username),
                'Username' => $username,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            return $e->getAwsErrorMessage();
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }

    /**
     * @param string $appClientId
     */
    public function setAppClientId($appClientId)
    {
        $this->appClientId = $appClientId;
    }

    /**
     * @param string $appClientSecret
     */
    public function setAppClientSecret($appClientSecret)
    {
        $this->appClientSecret = $appClientSecret;
    }

    /**
     * @param CognitoIdentityProviderClient $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @param string $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @param string $userPoolId
     */
    public function setUserPoolId($userPoolId)
    {
        $this->userPoolId = $userPoolId;
    }


public function logout($accessToken)
    {
    try{
            $response = $this->client->globalSignOutAsync([
                'AccessToken' => $accessToken, // REQUIRED
            ]);
             unset($_SESSION['AccessToken']);
             unset($_SESSION['sub_id']);
            return $response;
        } catch (Exception $e) {
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }

    /**
     * @param string $accessToken
     * @return array
     * @throws TokenVerificationException
     */
    public function decodeAccessToken($accessToken)
    {
        $algorithmManager = AlgorithmManager::create([
            new RS256(),
        ]);

        $serializerManager = new CompactSerializer(new StandardConverter());

        $jws = $serializerManager->unserialize($accessToken);
        $jwsVerifier = new JWSVerifier(
            $algorithmManager
        );

        $keySet = $this->getJwtWebKeys();
        if (!$jwsVerifier->verifyWithKeySet($jws, $keySet, 0)) {
            throw new TokenVerificationException('could not verify token');
        }

        return json_decode($jws->getPayload(), true);
    }

    /**
     * Verifies the given access token and returns the username
     *
     * @param string $accessToken
     *
     * @throws TokenExpiryException
     * @throws TokenVerificationException
     *
     * @return string
     */
    public function verifyAccessToken($accessToken)
    {
        $jwtPayload = $this->decodeAccessToken($accessToken);

        $expectedIss = sprintf('https://cognito-idp.%s.amazonaws.com/%s', $this->region, $this->userPoolId);
        if ($jwtPayload['iss'] !== $expectedIss) {
            throw new TokenVerificationException('invalid iss');
        }

        if ($jwtPayload['token_use'] !== 'access') {
            throw new TokenVerificationException('invalid token_use');
        }

        if ($jwtPayload['exp'] < time()) {
            throw new TokenExpiryException('invalid exp');
        }

        return $jwtPayload['username'];
    }

    /**
     * @param string $username
     *
     * @return string
     */
    public function cognitoSecretHash($username)
    {
        return $this->hash($username . $this->appClientId);
    }



    /**
     * @param $username
     *
     * @return \Aws\Result
     * @throws Exception
     */
    public function getGroups()
    {
        try {
            if(isset($_SESSION['AccessToken'])){
                return $this->client->listGroups([
                    'UserPoolId' => $this->userPoolId, // REQUIRED
                ]);
            }
        } catch (Exception $e) {
            throw CognitoResponseException::createFromCognitoException($e);
        }

    }



    /**
     * @param $groupName
     *
     * @return \Aws\Result
     * @throws Exception
     */
    public function deleteGroup($groupName)
    {
        try {
             return $this->client->deleteGroup([
                'GroupName' => $groupName, // REQUIRED
                'UserPoolId' => $this->userPoolId, // REQUIRED
            ]);
        } catch (CognitoIdentityProviderException $e) {
            return $e->getAwsErrorMessage();
        }

    }
   
    /**
     * @param $username
     *
     * @return \Aws\Result
     * @throws Exception
     */
       public function checkUserExistInGroup($username){
             try {
                 $this->client->adminListGroupsForUser([
                    'UserPoolId' => $this->userPoolId,
                    'Username'   => $username
                ]);
                return true;
            } catch (CognitoIdentityProviderException $e) {
                return false;
            }
       }

    /**
     * @param $username
     *
     * @return \Aws\Result
     * @throws Exception
     */
    public function getGroupsForUsername($username)
    {
        try {
            return $this->client->adminListGroupsForUser([
                'UserPoolId' => $this->userPoolId,
                'Username'   => $username
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw CognitoResponseException::createFromCognitoException($e);
        }

    }


    public function getUserGroup($groupName){
        try {
             $this->client->getGroup([
                'GroupName' => $groupName, // REQUIRED
                'UserPoolId' => $this->userPoolId, // REQUIRED
            ]);
             return true;
        } catch (CognitoIdentityProviderException $e) {
             return false;
        }
    }

    /**
     * @param string $message
     *
     * @return string
     */
    protected function hash($message)
    {
        $hash = hash_hmac(
            'sha256',
            $message,
            $this->appClientSecret,
            true
        );

        return base64_encode($hash);
    }

    /**
     * @param array $response
     * @return array
     * @throws ChallengeException
     * @throws Exception
     */
    public function poolclient(){
        try{
            if(isset($_SESSION['AccessToken'])){
                return $this->client->listUsers([
                    'UserPoolId' => $this->userPoolId,
                ]);
            }else{
               return false; 
            }
        }catch(CognitoIdentityProviderException $e){
            return $e->getAwsErrorMessage();
        }
    }

    /**
     * @param array $response
     * @return array
     * @throws ChallengeException
     * @throws Exception
     */
    public function removeUserFromGroup($groupname,$username){
        try{
            return $this->client->adminRemoveUserFromGroup([
                'GroupName' => $groupname, // REQUIRED
                'UserPoolId' => $this->userPoolId, // REQUIRED
                'Username' => $username, // REQUIRED
            ]);
        }catch(CognitoIdentityProviderException $e){
            return $e->getAwsErrorMessage();
        }
    }




    public function is_logged(){
       return  isset($_SESSION['AccessToken'])?true:false;
    }

    /**
     * @param array $response
     * @return array
     * @throws ChallengeException
     * @throws Exception
     */
    protected function handleAuthenticateResponse(array $response)
    {
        if (isset($response['AuthenticationResult'])) {
            return $response['AuthenticationResult'];
        }

        if (isset($response['ChallengeName'])) {
            throw ChallengeException::createFromAuthenticateResponse($response);
        }

        throw new Exception('Could not handle AdminInitiateAuth response');
    }

    /**
     * @param array $attributes
     * @return array
     */
    private function buildAttributesArray(array $attributes): array
    {
        $userAttributes = [];
        foreach ($attributes as $key => $value) {
            $userAttributes[] = [
                'Name' => (string)$key,
                'Value' => (string)$value,
            ];
        }
        return $userAttributes;
    }

    /**
    * @param array $response
    * @param Aws\Result Object
    */
    public function buildFormatedObject($userArray){
        try{
            $userAttributes['Username'] = $userArray['Username'];
            $userAttributes['UserCreateDate'] = $userArray['UserCreateDate'];
            $userAttributes['UserStatus'] = $userArray['UserStatus'];
            foreach ($userArray['UserAttributes'] as $key => $value) {
               $userAttributes[$value['Name']] = $value['Value'];
                
            }
            return $userAttributes;
        }catch(Exception $e){

        }
    }

    /**
    * @param array $response
    * @param Aws\Result Object
    */
    public function buildAdminFormatedObject($userArray){
        try{
            foreach ($userArray['Users'] as $key => $user) {
                $userAttributes['Enabled'] = (Boolean)$user['Enabled'];
                $userAttributes['Username'] = $user['Username'];
                $userAttributes['UserCreateDate'] = $user['UserCreateDate'];
                $userAttributes['UserStatus'] = $user['UserStatus'];
                foreach ($user['Attributes'] as $key => $value) {
                   $userAttributes[$value['Name']] = $value['Value'];
                    
                }
                $adminUserAttributes[] = $userAttributes;
            }
            return $adminUserAttributes;
        }catch(Exception $e){

        }
    }
}
