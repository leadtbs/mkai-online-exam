<template>
    <div>
        <QuestionCardHeader 
            :tab_index="tabIndex"
        />
        <div class="card-body">
            <div>
                <b-tabs content-class="mt-3" pills vertical>
                    <b-tab @click="tabIndex = 0" title="All Questions" active>
                        <QuestionList :set_id="set_id" :tab_index="0" :set_type="set_type"></QuestionList>
                    </b-tab>
                    <b-tab v-for="(tab, index) in tabs" :key="index" @click="tabIndex = tab.id" :title="tab.name">
                        <QuestionList :set_id="set_id" :tab_index="tab.id" :set_type="set_type"></QuestionList>
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
            set_type: ''
        }
    },
    components: {
        QuestionCardHeader,
        QuestionList
    },
    mounted() {
        switch(this.$route.params.set_type){
            case 'jlt': this.set_type = 1; break;
            case 'nce': this.set_type = 2; break;
            case 'ncj': this.set_type = 3; break;
            default: console.log('mao ni');
        }
        this.$axios.get('api/get_tabs', {
            params: {
                set_type: this.set_type
            }
        })
        .then(({data}) => {
            this.tabs = data;
        })
    }
}
</script>