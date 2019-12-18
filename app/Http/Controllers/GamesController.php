<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class GamesController
{
    protected static $instance;

    /**
     * Get Instance Games Controller
     * @return mixed
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new GamesController();
        }

        return static::$instance;
    }

    /**
     * Get all games
     * @return mixed
     */
    public static function getAllGames()
    {
        return Session::get('games');
    }

    /**
     * Get game
     * @param $id
     * @return mixed
     */
    public static function getGame($id)
    {
        return Session::get('games.' . $id)[0];
    }

    /**
     * Save game
     * @param $id
     * @param $game
     */
    public static function setGame($id, $game)
    {
        Session::push('games.' . $id, $game);
    }

    /**
     * Delete game
     * @param $id
     * @return bool
     */
    public static function deleteGame($id)
    {
        if (Session::exists('games.' . $id)) {
            Session::forget('games.' . $id);
            return true;
        }

        return false;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

}
