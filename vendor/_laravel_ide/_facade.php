<?php

namespace Illuminate\Support\Facades;

interface Auth
{
    /**
     * @return \App\Models\Gebruiker|false
     */
    public static function loginUsingId(mixed $id, bool $remember = false);

    /**
     * @return \App\Models\Gebruiker|false
     */
    public static function onceUsingId(mixed $id);

    /**
     * @return \App\Models\Gebruiker|null
     */
    public static function getUser();

    /**
     * @return \App\Models\Gebruiker
     */
    public static function authenticate();

    /**
     * @return \App\Models\Gebruiker|null
     */
    public static function user();

    /**
     * @return \App\Models\Gebruiker|null
     */
    public static function logoutOtherDevices(string $password);

    /**
     * @return \App\Models\Gebruiker
     */
    public static function getLastAttempted();
}