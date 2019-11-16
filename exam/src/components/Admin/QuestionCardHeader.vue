<template>
    <div class="card-header font-weight-bold">
        <router-link :to="{ name: 'set' }" class="btn btn-xs btn-info mr-3"><font-awesome-icon class="fa-fw" size="lg" icon="angle-left" /></router-link>
        Questions for <span class="text-primary">{{ setName }}</span>
        <b-button variant="success" size="sm" class="float-right">Add Question <font-awesome-icon class="fa-fw" icon="plus-square" /></b-button>
    </div>
</template>

<script>
export default {
    props: [
        'setName'
    ]
}
</script>