<template>
    <div>
        <QuestionCardHeader 
            :tab_index="tabIndex"
        />
        <div class="card-body">
            <div>
                <b-tabs content-class="mt-3" pills vertical>
                    <b-tab @click="tabIndex = 0" title="All Questions" active>
                        0
                    </b-tab>
                    <b-tab @click="tabIndex = 1" title="Script &amp; Vocabulary">
                        1
                    </b-tab>
                    <b-tab @click="tabIndex = 2" title="Grammar &amp; Conversation">
                        2
                    </b-tab>
                    <b-tab @click="tabIndex = 3" title="Listening Comprehension">
                        3
                    </b-tab>
                    <b-tab @click="tabIndex = 4" title="Reading Comprehension">
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
        }
    },
    components: {
        QuestionCardHeader
    }
}
</script>