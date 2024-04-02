<?php

namespace SocialiteProviders\Mailru;

use SocialiteProviders\Manager\OAuth2\User;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;

class Provider extends AbstractProvider
{
    const IDENTIFIER = 'MAILRU';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://oauth.mail.ru/login', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://oauth.mail.ru/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $params = [
            'access_token' => $token,
        ];

        $response = $this->getHttpClient()->get(
            'https://oauth.mail.ru/userinfo?' . http_build_query($params)
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['nickname'],
            'name'     => $user['name'],
            'email'    => $user['email'],
            'avatar'   => $user['image'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeFields($state = null)
    {
        return array_merge(parent::getTokenFields($state), [
            'state' => 'ignored_string',
            'response_type' => 'code',
        ]);
    }
}