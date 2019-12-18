<template>
    <div class="text-center mt-4">

        <div class="d-inline-block">'----------------'</div>
        <br>
        <div class="d-block" v-for="(value, row) in data">
            <div :disabled="stepUser ? true : false" class="d-inline-block pointer" v-for="(item, key) in value"
                 :data-line="row" :data-elem="key"
                 :data-value="item" @click="makeStep($event)">
                <span v-if="key===0">|</span>&nbsp;&nbsp;{{item}}&nbsp; |
            </div>
            <div v-if="row !==2 && row <= 1">----+----+----<br></div>
        </div>
        <div class="d-inline-block">'----------------'</div>

        <p v-if="stepUser">PC Thinking...</p>
        <br>
        <button class="mt-4" @click="deleteGame()">Delete this Game</button>

    </div>
</template>

<style scoped>
    .pointer {
        cursor: pointer;
    }

    .pointer:hover {
        background-color: lightblue;
    }
</style>

<script>
    export default {
        props: ['id'],

        data() {
            return {
                stopGame: false,
                stepUser: false,
                game: {
                    id: this.id,
                    board: '',
                    status: '',
                },
                data: {},
            }
        },

        mounted() {
            this.loadGame();
        },

        watch: {
            'game.board': function () {
                let data = {};
                for (let i = 0; i < 3; i++) {
                    data[i] = [];
                    for (let j = 0; j < this.game.board.length; j++) {
                        if (j >= (i * 3) && j < ((i + 1) * 3)) {
                            data[i].push(this.game.board.charAt(j));
                        }
                    }
                }

                let keyId = this.getKeyGame();
                if (!localStorage[keyId]) {
                    if (confirm('Would you like play by "X" ?')) {
                        localStorage[keyId] = 'X';
                    } else {
                        localStorage[keyId] = 'O';
                    }
                }

                this.data = data;
            },

            'game.status': function () {
                if (this.game.status !== 'RUNNING') {
                    this.stopGame = true;
                    alert(this.game.status);
                }
            }
        },


        methods: {
            /**
             * Get game key for localStorage
             */
            getKeyGame: function () {
                return this.game.id.split('-').join('');
            },

            /**
             * Load game by id
             */
            loadGame: function () {
                let gameId = this.game.id;
                this.$http.get('/api/v1/games/' + gameId).then(res => {
                    this.game = res.data;
                });
            },

            /**
             * Main logic for move and trigger function thinkPC
             */
            makeStep: function (event) {
                if (this.stopGame) {
                    alert(this.game.status);
                    return;
                }

                this.stepUser = true;
                let elem = event.currentTarget,
                    value = elem.getAttribute('data-value'),
                    line = parseInt(elem.getAttribute('data-line')),
                    item = parseInt(elem.getAttribute('data-elem')),
                    position;

                switch (line) {
                    case 0:
                        position = line + item;
                        break;
                    case 1:
                        position = line + item + 2;
                        break;
                    case 2:
                        position = line + item + 4;
                        break;
                }

                if (value === 'O' || value === 'X') {
                    alert('You can\'t do this Step');
                } else {
                    this.game.board = this.game.board.substr(0, position)
                        + window.localStorage.getItem(this.getKeyGame())
                        + this.game.board.substr(position + 1);

                    let data = {
                        game_id: this.game.id,
                        game: {
                            board: this.game.board,
                        }
                    };

                    this.thinkPC(data);
                }
            },

            /**
             * Think PC
             * Send request with out move to server
             */
            thinkPC: function (data) {
                let _self = this;
                setTimeout(function () {
                    _self.$http.put('/api/v1/games/' + _self.game.id, data).then(res => {
                        if (res.status === 200) {
                            _self.game = res.data;
                            _self.stepUser = false;
                        } else {
                            console.log('Something went wrong');
                        }
                    });
                }, 1000)
            },

            /**
             * Delete game
             * if 200 close game and show main menu
             */
            deleteGame: function () {
                this.$http.delete('/api/v1/games/' + this.game.id).then(res => {
                    if (res.status === 200) {
                        this.$emit('deleteGame', true);
                    }
                });
            }
        }
    }
</script>