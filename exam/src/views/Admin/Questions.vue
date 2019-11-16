<template>
    <div>
        <QuestionCardHeader 
            :setName="setName"
        />
        <div class="card-body">
            <div>
                <b-tabs content-class="mt-3" pills vertical>
                    <b-tab title="All Questions" active>
                        0
                    </b-tab>
                    <b-tab title="Script &amp; Vocabulary">
                        1
                    </b-tab>
                    <b-tab title="Grammar &amp; Conversation">
                        2
                    </b-tab>
                    <b-tab title="Listening Comprehension">
                        3
                    </b-tab>
                    <b-tab title="Reading Comprehension">
                        4
                    </b-tab>
                </b-tabs>
            </div>
        </div>
    </div>
</template>

<script>
import QuestionCardHeader from '@/components/Admin/QuestionCardHeader'

export default {
    name: 'questions',
    data() {
        return {
            tabIndex: 0,
            setName: ''
        }
    },
    created() {
        this.$axios.get('/api/set_name/'+this.$route.params.set_id)
        .then(({ data }) => {
            this.setName = data
        })
    },
    components: {
        QuestionCardHeader
    }
}
</script>