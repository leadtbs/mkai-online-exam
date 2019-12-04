<template>
    <div>
        <QuestionCardHeader 
            :tab_index="$route.params.tab_index"
            editCounter="true"
        />

    </div>
</template>

<script>
import QuestionCardHeader from '@/components/Admin/QuestionCardHeader'

export default {
    name: 'editQuestion',
    components: {
        QuestionCardHeader
    }
}
</script>