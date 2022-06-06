<template>
    <div v-if="errors" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
        <ul v-if="isValidationError" v-for="(v, k) in errors" :key="k" class='marker:text-red-500 list-outside list-disc ml-6'>
            <li v-for="error in v" :key="error">{{ error }}</li>
        </ul>
        <ul v-else class='marker:text-red-500 list-outside list-disc ml-6'>
            <li >{{ errors }}</li>
        </ul>
    </div>
</template>

<script>
export default {
    name: "ErrorComponent",
    props: ['errors'],
    computed: {
        isValidationError() {
            return typeof this.errors === 'object'
        }
    }
}
</script>
