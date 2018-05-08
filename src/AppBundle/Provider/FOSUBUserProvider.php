<?php

namespace AppBundle\Provider;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{

    public function connect(UserInterface $user, UserResponseInterface $response) {
        $property = $this->getProperty($response);

        $username = $response->getUsername();

        // On connect, retrieve the access token and the user id
        $service = $response->getResourceOwner()->getName();

        $setter = 'set' . ucfirst($service);
        $setter_id = $setter . 'Id';
        // $setter_token = $setter . 'AccessToken';

        // Disconnect previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            // $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        // Connect using the current user
        $user->$setter_id($username);
        // $user->$setter_token($response->getAccessToken());
        $this->userManager->updateUser($user);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response) {
        // $data = $response->getResponse();
        //dump($response);
        $service = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($service);
        $setter_id = $setter . 'Id';

        $username = $response->getUsername();
        $email = $response->getEmail() ? $response->getEmail() : $username;
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));
        //dump($this->getProperty($response));
        //dump($username);
        //dump($user);

        if (($user = $this->userManager->findUserBy(array($this->getProperty($response) => $username))) !== null) {
            // If the user exists with socialId, use the HWIOAuth
            $user = parent::loadUserByOAuthUserResponse($response);
        } elseif (($user = $this->userManager->findUserBy(array('email' => $email))) !== null) {
            // If the user exists with email, set the socialId
            //dump($username);
            //dump($setter_id);
            //dump($user);
            //exit;
            $user->$setter_id($username);
            $this->userManager->updateUser($user);
        } else {
            // Create user
            // $setter_token = $setter . 'AccessToken';
            // create new user here
            $user = $this->userManager->createUser();
            $user->$setter_id($username);
            // $user->$setter_token($response->getAccessToken());

            //I have set all requested data with the user's username
            //modify here with relevant data
            $user->setUsername($this->generateRandomUsername($username, $response->getResourceOwner()->getName()));
            $user->setEmail($email);
            $user->setPassword($username);
            $user->addRole('ROLE_PARTICULIER');
            $user->setEnabled(true);
            $this->userManager->updateUser($user);
        }


        // If the user exists, use the HWIOAuth
        //$user = parent::loadUserByOAuthUserResponse($response);

        //$serviceName = $response->getResourceOwner()->getName();

        // $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        // Update the access token
        // $user->$setter($response->getAccessToken());

        return $user;
    }

    /**
     * Generates a random username with the given
     * e.g 12345_github, 12345_facebook
     *
     * @param string $username
     * @param type $serviceName
     * @return type
     */
    private function generateRandomUsername($username, $serviceName){
        if(!$username){
            $username = "user". uniqid((rand()), true) . $serviceName;
        }

        return $username. "_" . $serviceName;
    }
}