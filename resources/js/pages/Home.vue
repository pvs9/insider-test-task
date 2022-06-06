<template>
    <div class="mt-10 sm:mt-0">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Football league creation form</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Choose a name of a new football league, its type and then choose the teams if you want or they will be chosen automatically.
                    </p>
                </div>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <form @submit.prevent="createLeague">
                    <div class="shadow sm:rounded-md">
                        <div class="px-4 py-5 bg-white sm:p-6">
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input v-model="form.name" type="text" name="name" id="name" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required/>
                                </div>
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                                    <Listbox id="type" v-model="form.type">
                                        <div class="relative mt-1">
                                            <ListboxButton
                                                class="relative w-full cursor-default rounded-lg bg-white py-2 pl-3 border border-gray-300 pr-10 text-left shadow-md sm:text-sm"
                                            >
                                                <span class="block truncate">{{ typePlaceholder }}</span>
                                                <span
                                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2"
                                                >
                                                    <SelectorIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                                                </span>
                                            </ListboxButton>

                                            <transition
                                                leave-active-class="transition duration-100 ease-in"
                                                leave-from-class="opacity-100"
                                                leave-to-class="opacity-0"
                                            >
                                                <ListboxOptions
                                                    class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 sm:text-sm"
                                                >
                                                    <ListboxOption
                                                        v-slot="{ active, selected }"
                                                        v-for="type in types"
                                                        :key="type.name"
                                                        :value="type"
                                                        as="template"
                                                    >
                                                        <li
                                                            :class="[
                                                              active ? 'bg-violet-400 text-gray-900' : 'text-gray-900',
                                                              'relative cursor-default select-none py-2 pl-10 pr-4',
                                                            ]"
                                                        >
                                                            <span
                                                                :class="[
                                                                selected ? 'font-medium' : 'font-normal',
                                                                'block truncate',
                                                              ]"
                                                            >
                                                                {{ type.name }}
                                                            </span>
                                                            <span
                                                                v-if="selected"
                                                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-violet-600"
                                                            >
                                                              <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                                            </span>
                                                        </li>
                                                    </ListboxOption>
                                                </ListboxOptions>
                                            </transition>
                                        </div>
                                    </Listbox>
                                </div>
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="teams" class="block text-sm font-medium text-gray-700">Teams <span class="font-extralight">(optional)</span></label>
                                    <Listbox id="teams" v-model="form.teams" multiple>
                                        <div class="relative mt-1">
                                            <ListboxButton
                                                class="relative w-full cursor-default rounded-lg bg-white py-2 pl-3 border border-gray-300 pr-10 text-left shadow-md sm:text-sm"
                                            >
                                                <span class="block truncate">{{ teamsPlaceholder }}</span>
                                                <span
                                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2"
                                                >
                                                    <SelectorIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                                                </span>
                                            </ListboxButton>

                                            <transition
                                                leave-active-class="transition duration-100 ease-in"
                                                leave-from-class="opacity-100"
                                                leave-to-class="opacity-0"
                                            >
                                                <ListboxOptions
                                                    class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 sm:text-sm"
                                                >
                                                    <ListboxOption
                                                        v-slot="{ active, selected }"
                                                        v-for="team in teams"
                                                        :key="team.name"
                                                        :value="team"
                                                        as="template"
                                                    >
                                                        <li
                                                            :class="[
                                                              active ? 'bg-violet-400 text-gray-900' : 'text-gray-900',
                                                              'relative cursor-default select-none py-2 pl-10 pr-4',
                                                            ]"
                                                        >
                                                            <span
                                                                :class="[
                                                                selected ? 'font-medium' : 'font-normal',
                                                                'block truncate',
                                                              ]"
                                                            >
                                                                {{ team.name }}
                                                            </span>
                                                            <span
                                                                v-if="selected"
                                                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-violet-600"
                                                            >
                                                              <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                                            </span>
                                                        </li>
                                                    </ListboxOption>
                                                </ListboxOptions>
                                            </transition>
                                        </div>
                                    </Listbox>
                                </div>
                                <div class="col-span-6 sm:col-span-3">
                                    <ErrorComponent :errors="errors"/>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white dark:bg-violet-400 dark:text-gray-900">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import {
    Listbox,
    ListboxButton,
    ListboxOptions,
    ListboxOption,
} from '@headlessui/vue'
import { CheckIcon, SelectorIcon } from '@heroicons/vue/solid'
import ErrorComponent from "../components/ErrorComponent";

export default {
    name: "Home",
    components: {
        CheckIcon,
        ErrorComponent,
        Listbox,
        ListboxButton,
        ListboxOptions,
        ListboxOption,
        SelectorIcon,
    },
    data() {
        return {
            errors: null,
            form: {
                name: null,
                teams: [],
                type: null
            },
            teams: [],
            types: [
                {
                    id: 1,
                    name: 'Insider League'
                }
            ]
        }
    },
    computed: {
        teamsPlaceholder() {
            return this.form.teams.length > 0 ?
                this.form.teams.map((team) => team.name).join(', ') :
                'Choose teams'
        },
        typePlaceholder() {
            return this.form.type !== null ?
                this.form.type.name :
                'Choose league type'
        }
    },
    mounted() {
        this.getTeams()
    },
    methods:{
        getFormData() {
            return {
                name: this.form.name || null,
                teams: this.form.teams || [],
                type: this.form.type?.id || null,
            }
        },
        getTeams() {
            this.$axios
                .get('team')
                .then(response => {
                    this.teams = response.data.data
                })
                .catch(error => {
                    this.errors = error.response.data?.errors || 'Some server error occurred';
                });
        },
        createLeague() {
            this.$axios
                .post('league', this.getFormData())
                .then(response => {
                    this.errors = null

                    this.$router.push('/league/' + response.data.data.id)
                })
                .catch(error => {
                    this.errors = error.response.data?.errors || 'Some server error occurred';
                });
        }
    }
}
</script>
