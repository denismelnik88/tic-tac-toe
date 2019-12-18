<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Game id
     * @var string
     */
    public $id;

    /**
     * Game board
     * @var string
     */
    public $board;

    /**
     * Game status
     * @var mixed
     */
    public $status;

    /**
     * Game combinations
     * @var array
     */
    protected $lines;

    /**
     * User symbol
     * @var null
     */
    public $userSymbol;

    /**
     * Allow status
     */
    private const STATUS = array('RUNNING', 'X_WON', 'O_WON', 'DRAW');

    /**
     * GameController constructor.
     */
    public function __construct()
    {
        $this->id = self::generateId();
        $this->status = self::STATUS[0];
        $this->userSymbol = null;
    }

    /**
     * Set board
     * @param $value
     */
    public function setBoard($value)
    {
        $this->board = $value;
    }

    /**
     * Generate Id
     * @return string
     */
    private function generateId()
    {
        $gener = function ($length = 10) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        };

        return $gener(8) . '-' . rand(1000, 9999) . '-' . rand(1000, 9999) . '-' . $gener(4) . '-' . $gener(12);
    }

    /**
     * Find and set User symbol
     * @param $board
     */
    public function setUserSymbol($board)
    {
        $data = str_split($board);
        if (count(array_keys($data, '-')) == 8) {
            $this->userSymbol = array_values(array_diff($data, array('-')))[0];
        }
    }

    /**
     * Check PC move then Game loaded
     */
    public function checkPCMove()
    {
        $tmp = str_split($this->board);
        if (count(array_keys($tmp, '-')) % 2 == 0) {
            $this->move($this->board);
        }
    }

    /**
     * Get all combinations
     * @param $tmp
     */
    private function getAllLines($tmp)
    {
        $this->getHorizontalLines($tmp);
        $this->getVerticalLines($tmp);
        $this->getCrossLines($tmp);
    }

    /**
     * Get Horizontal Lines
     * @param $tmp
     */
    private function getHorizontalLines($tmp)
    {
        $this->lines = array_chunk($tmp, 3, true);
    }

    /**
     * Get Vertical Lines
     * @param $tmp
     */
    private function getVerticalLines($tmp)
    {
        for ($i = 0; $i < 3; $i++) {
            $key = count($this->lines);
            $this->lines[$key] = [];

            for ($j = $i; $j < count($tmp); $j += 3) {
                if (count($this->lines[$key]) == 3) continue;
                $this->lines[$key][$j] = $tmp[$j];
            }

        }
    }

    /**
     * Get Cross Lines
     * @param $tmp
     */
    private function getCrossLines($tmp)
    {
        for ($i = 0; $i <= 2; $i += 2) {
            $key = count($this->lines);
            $this->lines[$key] = [];
            $value = 2;
            if ($i == 0) $value = 4;

            for ($j = $i; $j < count($tmp); $j += $value) {
                if (count($this->lines[$key]) == 3) continue;
                $this->lines[$key][$j] = $tmp[$j];
            }

        }
    }

    /**
     * Check Lines
     * @param $value
     * @return array
     */
    private function checkLines($value)
    {
        foreach ($this->lines as $line) {
            if (count(array_keys($line, $value)) == 3) {
                $status = $value . '_WON';
                $this->status = $status;

                return ['winner' => $status, 'value' => $value, 'line' => $line];
            }
        }

        return ['winner' => false, 'value' => null];
    }

    /**
     * Do move user and PC
     * @param $board
     * @throws \Exception
     */
    public function move($board)
    {
        $tmp = str_split($board);
        $this->getAllLines($tmp);
        $winner = $this->checkLines($this->userSymbol);

        if ($winner['winner']) {
            $this->status = $winner['winner'];
        } elseif (count(array_keys($tmp, '-')) == 9) {
            $this->status = self::STATUS[3];
        } else {
            $tmp = $this->movePC($tmp);

            $winner = $this->checkLines($this->userSymbol);
            if ($winner['winner']) {
                $this->status = $winner['winner'];
            }
        }

        $this->board = implode('', $tmp);
    }

    /**
     * Move for PC uses in function move()
     * @param $tmp
     * @return mixed
     * @throws \Exception
     */
    private function movePC($tmp)
    {
        do {
            $key = random_int(0, (count($tmp) - 1));
        } while (!$this->isFree($tmp[$key]));

        $tmp[$key] = $this->getPCSymbol();

        return $tmp;
    }

    /**
     * Check is free field
     * @param $value
     * @return bool
     */
    private function isFree($value)
    {
        return ($value == '-') ? true : false;
    }

    /**
     * Get PC Symbol
     * @return string
     */
    private function getPCSymbol()
    {
        return $this->userSymbol == 'X' ? 'O' : 'X';
    }
}
