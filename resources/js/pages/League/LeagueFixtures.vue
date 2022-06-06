<template>
    <div class="grid grid-flow-row grid-cols-2 auto-rows-max">
        <div v-for="(matches, week) in weekMatches">
            <div class="py-4 inline-block min-w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <table class="min-w-full text-center">
                        <thead class="border-b bg-violet-400">
                            <tr>
                                <th scope="col" colspan="3" class="text-sm font-medium text-white px-6 py-4">
                                    Week {{ week }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="match in matches" class="bg-white border-b">
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ match.house_team.name }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    -
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ match.away_team.name }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div>
            <div class="py-4 inline-block min-w-full sm:px-6 lg:px-8">
                <button v-if="simulationAvailable" @click.prevent="startSimulation" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white dark:bg-violet-400 dark:text-gray-900">Start simulation</button>
                <button v-else @click.prevent="visitProgress" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white dark:bg-violet-400 dark:text-gray-900">Show simulation</button>
            </div>
        </div>
        <div>
            <div class="py-4 inline-block min-w-full sm:px-6 lg:px-8">
                <ErrorComponent :errors="errors"/>
            </div>
        </div>
    </div>
</template>

<script>
import ErrorComponent from "../../components/ErrorComponent";

export default {
    name: "LeagueFixtures",
    props: ['id'],
    components: {
        ErrorComponent
    },
    data() {
        return {
            errors: null,
            simulationAvailable: false,
            weekMatches: []
        }
    },
    mounted() {
        this.getLeague()
    },
    methods:{
        getLeague() {
            this.$axios
                .get('league/' + this.id)
                .then(response => {
                    this.errors = null
                    this.simulationAvailable = response.data.data.progress_week === null
                    this.weekMatches = this.groupBy(response.data.data.matches, 'week')
                })
                .catch(error => {
                    this.errors = error.response.data?.errors || 'Some server error occurred';
                });
        },
        startSimulation() {
            this.$axios
                .post('league/' + this.id + '/progress')
                .then(response => {
                    this.errors = null

                    this.visitProgress()
                })
                .catch(error => {
                    this.errors = error.response.data?.errors || 'Some server error occurred';
                });
        },
        visitProgress() {
            this.$router.push({ name: 'league-progress', params: { id: this.id } })
        },
        groupBy(array, key) {
            return array.reduce(function(rv, x) {
                (rv[x[key]] = rv[x[key]] || []).push(x);
                return rv;
            }, {});
        }
    }
}
</script>
