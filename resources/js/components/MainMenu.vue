<template>
    <div v-if="!playGame" class="text-center mt-4">
        <button class="col-md-2 mb-2" @click="getAllGames()">Get all games</button>
        <br>
        <button class="col-md-2" @click="startNewGame()">Start New Game</button>

        <p v-for="(game,id) in games">
            {{game.id}} {{game.status}} <a @click.prevent="startGame(id)" :href="'/api/v1/games/' + id">Load this
            game</a>
        </p>
    </div>
    <div v-else>
        <tic-tac-toe :id="id" @deleteGame="closeGame()"></tic-tac-toe>
    </div>
</template>

<script>
    export default {
        props: [],

        data() {
            return {
                playGame: false,
                id: null,
                game: {
                    id: '',
                    board: '---------',
                    status: '',
                },
                games: [],
            }
        },

        watch: {
            id: function () {
                this.playGame = !this.playGame;
            }
        },

        methods: {
            /**
             * Get all games
             */
            getAllGames: function () {
                console.log('getAllGames');
                this.$http.get('/api/v1/games').then(res => {
                    if (res.status === 200) {
                        this.games = res.data;
                    } else {
                        alert('error');
                    }
                });
            },

            /**
             * Close games
             */
            closeGame: function () {
                this.playGame = false;
                this.games = [];
            },

            /**
             * Start New Game
             */
            startNewGame: function () {
                let params = {
                    game: JSON.stringify(this.getNewGame()),
                };

                this.$http.post('/api/v1/games', params).then(res => {
                    if (res.status === 201) {
                        this.id = res.data.location
                    }
                });
            },

            /**
             * Start saved game
             * @param id
             */
            startGame: function (id) {
                this.id = id;
            },

            /**
             * Get new Game for startNewGame()
             * @returns {Game}
             */
            getNewGame: function () {
                class Game {
                    constructor() {
                        this.board = '---------';
                    }
                }

                return new Game();
            },
        }
    }
</script>