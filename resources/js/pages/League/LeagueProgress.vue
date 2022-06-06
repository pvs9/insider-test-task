<template>
    <div v-if="league" class="grid grid-flow-row grid-cols-2">
        <div>
            <div class="py-4 min-w-full px-6">
                <div class="overflow-hidden">
                    <table class="min-w-full text-center">
                        <thead class="border-b bg-violet-400">
                            <tr>
                                <th scope="col" class="text-sm font-medium text-white px-6 py-4">Team Name</th>
                                <th scope="col" class="text-sm font-medium text-white px-6 py-4">PTS</th>
                                <th scope="col" class="text-sm font-medium text-white px-6 py-4">P</th>
                                <th scope="col" class="text-sm font-medium text-white px-6 py-4">W</th>
                                <th scope="col" class="text-sm font-medium text-white px-6 py-4">D</th>
                                <th scope="col" class="text-sm font-medium text-white px-6 py-4">L</th>
                                <th scope="col" class="text-sm font-medium text-white px-6 py-4">GD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="team in league.teams" class="bg-white border-b">
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ team.name }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ team.points }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ team.played }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ team.wins }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ team.draws }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ team.losses }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ team.goal_difference }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="py-4 min-w-full px-6">
                <div class="overflow-hidden">
                    <ErrorComponent :errors="errors" />
                </div>
            </div>
            <div class="py-4 min-w-full px-6">
                <div class="overflow-hidden">
                    <div class="flex space-x-4">
                        <button v-if="leagueProgressAvailable" @click.prevent="playAllWeeks" class="justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white dark:bg-violet-400">Play All Weeks</button>
                        <button v-if="leagueProgressAvailable" @click.prevent="playNextWeek" class="justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white dark:bg-violet-400">Play Next Week</button>
                        <button @click.prevent="resetData" class="justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white dark:bg-red-600">Reset Data</button>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="py-4 min-w-full px-6">
                <div class="overflow-hidden">
                    <table class="min-w-full text-center">
                        <thead class="border-b bg-violet-400">
                            <tr>
                                <th scope="col" colspan="3" class="text-sm font-medium text-white px-6 py-4">
                                    Week {{ league.progress_week }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="match in league.matches" class="bg-white border-b">
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ match.house_team.name }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ match.house_points }} - {{ match.away_points }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ match.away_team.name }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div v-if="shouldShowWinProbability" class="py-4 min-w-full px-6">
                <div class="overflow-hidden">
                    <table class="min-w-full text-center">
                        <thead class="border-b bg-violet-400">
                        <tr>
                            <th scope="col" class="text-sm font-medium text-white px-6 py-4">
                                Championship predictions
                            </th>
                            <th scope="col" class="text-sm font-medium text-white px-6 py-4">
                                %
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="team in league.teams" class="bg-white border-b">
                            <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                {{ team.name }}
                            </td>
                            <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                {{ team.win_probability }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ErrorComponent from "../../components/ErrorComponent";

export default {
    name: "LeagueProgress",
    props: ['id'],
    components: {
        ErrorComponent
    },
    data() {
        return {
            errors: null,
            league: null
        }
    },
    computed: {
        leagueProgressAvailable() {
            return this.league &&
                this.league.progress_week < this.league.total_weeks
        },
        shouldShowWinProbability() {
            return this.league &&
                this.league.total_weeks &&
                this.league.progress_week >= this.league.total_weeks / 2
        },
    },
    mounted() {
        this.getLeague()
    },
    methods:{
        getLeague() {
            this.$axios
                .get('league/' + this.id + '/progress')
                .then(response => {
                    this.errors = null
                    this.league = response.data.data
                })
                .catch(error => {
                    this.errors = error.response.data?.errors || 'Some server error occurred';
                });
        },
        playAllWeeks() {
            this.$axios
                .post('league/' + this.id + '/progress/all')
                .then(response => {
                    this.errors = null

                    this.getLeague()
                })
                .catch(error => {
                    this.errors = error.response.data?.errors || 'Some server error occurred';
                });
        },
        playNextWeek() {
            this.$axios
                .post('league/' + this.id + '/progress')
                .then(response => {
                    this.errors = null

                    this.getLeague()
                })
                .catch(error => {
                    this.errors = error.response.data?.errors || 'Some server error occurred';
                });
        },
        resetData() {
            this.$axios
                .post('league/' + this.id + '/reset')
                .then(response => {
                    this.errors = null

                    this.$router.push({ name: 'league', params: { id: this.id } })
                })
                .catch(error => {
                    this.errors = error.response.data?.errors || 'Some server error occurred';
                });
        }
    }
}
</script>
