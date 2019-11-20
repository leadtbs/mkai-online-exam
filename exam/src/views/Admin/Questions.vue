<template>
    <div>
        <QuestionCardHeader 
            :tab_index="tabIndex"
        />
        <div class="card-body">
            <div>
                <b-tabs content-class="mt-3" pills vertical>
                    <b-tab @click="tabIndex = 0" title="All Questions" active>
                        <QuestionList :set_id="set_id" :tab_index="0"></QuestionList>
                    </b-tab>
                    <b-tab v-for="(tab, index) in tabs" :key="index" @click="tabIndex = index+1" :title="tab.name">
                        <QuestionList :set_id="set_id" :tab_index="index+1"></QuestionList>
                    </b-tab>
                </b-tabs>
            </div>
        </div>
    </div>
</template>

<script>
import QuestionCardHeader from '@/components/Admin/QuestionCardHeader'
import QuestionList from '@/components/Admin/QuestionList'

export default {
    name: 'questions',
    data() {
        return {
            tabIndex: 0,
            tabs: [],
            set_id: this.$route.params.set_id,
        }
    },
    components: {
        QuestionCardHeader,
        QuestionList
    },
    mounted() {
        this.$axios.get('/api/get_tabs')
        .then(({data}) => {
            this.tabs = data;
        })
    }
}
</script>