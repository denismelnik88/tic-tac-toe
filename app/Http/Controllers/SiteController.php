<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteController extends Controller
{
    /** @var GamesController $gamesInstance */
    public $gamesInstance;

    /**
     * SiteController constructor.
     */
    public function __construct()
    {
        $this->gamesInstance = GamesController::getInstance();
    }

    /**
     * Get all games
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllGames()
    {
        return response()->json($this->gamesInstance::getAllGames(), 200);
    }

    /**
     * Start New Game
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function startNewGame(Request $request)
    {
        if (!$request->has('game')) {
            return response('Request not not fount', 400);
        }

        $data = json_decode($request->input('game'));
        $game = new GameController();
        $game->setBoard($data->board);

        $this->gamesInstance::setGame($game->id, $game);

        return response()->json(['location' => $game->id], 201);
    }

    /**
     * Do move
     * @param Request $request
     * @param $gameId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function moveGame(Request $request, $gameId)
    {
        if (!$request->has('game') || !$gameId || $gameId == '') {
            return response()->json(['reason' => 'Bad request'], 400);
        }

        /** @var GameController $game */
        $game = $this->gamesInstance::getGame($gameId);
        $data = $request->input('game');

        if (is_null($game->userSymbol)) {
            $game->setUserSymbol($data['board']);
        }

        $game->move($data['board']);

        return response()->json([
            "id" => $game->id,
            "board" => $game->board,
            "status" => $game->status,
        ], 200);

    }

    /**
     * Get one game
     * @param $gameId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGame($gameId)
    {
        /** @var GameController $game */
        $game = $this->gamesInstance::getGame($gameId);
        $game->checkPCMove();
        return response()->json([
            "id" => $game->id,
            "board" => $game->board,
            "status" => $game->status,
        ], 200);
    }

    /**
     * Delete game
     * @param $gameId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function deleteGame($gameId)
    {
        if ($this->gamesInstance::deleteGame($gameId)) {
            return response('Game successfully deleted', 200);
        }

        return response('Bad request', 400);
    }
}
