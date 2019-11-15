<template>
    <div class="card-header font-weight-bold">
        Quiz Set List
        <b-button variant="success" size="sm" class="float-right" v-b-modal.add-set-modal>Add Set <font-awesome-icon class="fa-fw" icon="plus-square" /></b-button>
    </div>
</template>

<script>
export default {
    name: 'set-card-header'
}
</script>