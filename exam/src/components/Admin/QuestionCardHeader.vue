<template>
    <div class="card-header font-weight-bold">
        <router-link :to="(addCounter || editCounter) ? { name: 'question' } : { name: 'sets' }" class="btn btn-xs btn-info mr-3"><font-awesome-icon class="fa-fw" size="lg" icon="angle-left" /></router-link>
        <span v-if="addCounter">Add </span>  
        <span v-if="editCounter">Edit Question</span>
        <span v-else>Questions for <span class="text-primary">{{ setName }} </span></span>
        <span v-if="addCounter">under <span class="text-info">{{ tabName }}</span></span>
        <template v-if="addCounter || editCounter">
            <b-button v-if="addCounter && ifJLT" @click="addChoiceForm" variant="success" size="sm" class="float-right">Add Choices <font-awesome-icon class="fa-fw" icon="plus-square" /></b-button>
            <div class="float-right mr-3">
                <p-check v-if="addCounter" v-model="choice_type" @change="choiceTypeChange" class="p-icon p-plain p-smooth" toggle style="font-size: 25px;">
                    <i slot="extra" class="icon fa fa-image text-primary"></i>
                    <i slot="off-extra" class="icon fa fa-font text-info"></i>
                </p-check>
            </div>
        </template>
        <template v-else>
            <b-button @click="addQuestion(tab_index+1)" variant="success" size="sm" class="float-right" :disabled="tab_index == 0">Add Question <font-awesome-icon class="fa-fw" icon="plus-square" /></b-button>
        </template>
    </div>
</template>

<script>
export default {
    props: [
        'tab_index', 'addCounter', 'editCounter', 'ifJLT'
    ],
    data() {
        return {
            setName: '',
            tabName: '',
            choice_type: false,
        }
    },
    methods: {
        addQuestion(tab_index){
            this.$router.push({ name: 'add_question', params: {tab_index} })
        },
        choiceTypeChange(){
            this.$emit('triggerChoiceTypeChange', this.choice_type);
        },
        addChoiceForm(){
            this.$emit('triggerAddChoiceForm');
        }
    },
    created() {
        this.$axios.get('/api/set_name/'+this.$route.params.set_id)
        .then(({ data }) => {
            this.setName = data
        })

        if(this.addCounter && this.tab_index){
            this.$axios.get('/api/tab_name/'+this.tab_index, {
                params: {
                    set_type: this.$route.params.set_type
                }
            })
            .then(({ data }) => {
                this.tabName = data;
            })
        }
    }
}
</script>