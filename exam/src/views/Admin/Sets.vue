<template>
    <div>
        <div class="card-header font-weight-bold">
            Quiz Set List
            <b-button @click="clearForm" variant="success" size="sm" class="float-right" v-b-modal.add-set-modal>Add Set <font-awesome-icon class="fa-fw" icon="plus-square" /></b-button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mt-2 p-0" v-for="set in setList" :key="set.id">
                    <table class="text-center table-bordered m-auto" style="width: 95%;">
                        <tr>
                            <td style="height: 50px;" colspan="5" class="align-middle font-weight-bold set-pick">{{ set.name }}</td>
                        </tr>
                        <tr>
                            <td class="align-middle" style="width:20%"><font-awesome-icon class="fa-fw text-info" icon="clock" />{{ set.time }}</td>
                            <td class="align-middle" style="width:15%"><font-awesome-icon class="fa-fw text-success" icon="question" />5</td>
                            <td class="align-middle" style="width:15%"><font-awesome-icon class="fa-fw text-success" icon="star" />5</td>
                            <td class="align-middle" style="width:10%">
                                <b-button variant="primary" size="xs" @click="editSetForm(set)"><font-awesome-icon icon="edit" /></b-button>
                            </td>
                            <td class="align-middle" style="width:10%">
                                <b-button variant="danger" size="xs"><font-awesome-icon icon="trash" /></b-button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
            
        <b-modal centered
            id="add-set-modal"  
            title="Set"
            ok-title="Save Changes"
            ref="add-set-modal"
            @ok="submitForm">
            <b-form @submit.prevent="submitForm">

                <b-form-group>
                    <b-form-input
                    type="text"
                    required
                    placeholder="Set Name"
                    v-model="form.name"
                    ></b-form-input>
                </b-form-group>

                <b-form-group>
                    <TimePicker 
                        autocomplete="off" 
                        placeholder="Duration HH:MM"
                        name="time"
                        v-model="form.time"
                        :config="options">
                    </TimePicker>
                </b-form-group>

                <b-form-group>
                    <b-form-input
                    type="password"
                    required
                    placeholder="Set Password"
                    v-model="form.password"
                    ></b-form-input>
                </b-form-group>

                <b-form-group class="d-none">
                    <b-button type="submit">Save Changes</b-button>
                </b-form-group>

            </b-form>
        </b-modal>
    </div>
</template>

<script>
import TimePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import jQuery from 'jquery'
import { Form } from 'vform'

jQuery.extend(true, jQuery.fn.datetimepicker.defaults, {
    icons: {
        time: 'fas fa-clock',
        up: 'fas fa-arrow-up',
        down: 'fas fa-arrow-down',
        clear: 'fa fa-trash-alt',
        close: 'fa fa-times-circle'
    }
});

export default {
    name: 'sets',
    data(){
        return {
            setList: {},
            options: {
                format: 'H:mm',
                useCurrent: false,
                showClear: true,
                showClose: true
            },
            form: new Form({
                id: '',
                name: '',
                time: '',
                password: ''
            }),
            editState: false,
        }
    },
    methods: {
        editSetForm(set){
            this.clearForm();
            this.editState = true;
            this.form.fill(set);
            this.$refs['add-set-modal'].show();
        },
        submitForm(){
            this.$Progress.start();
            if(this.form.password == ''){
                this.form.password = undefined;
            }
            if(this.editState == false){
                this.$axios.post('api/save_set', this.form)
                .then(() => {
                    this.$refs['add-set-modal'].hide();
                    this.$Progress.finish();
                    this.loadSets();
                })
                .catch(() => {
                    this.$Progress.fail();
                })
            }
        },
        loadSets(){
            this.$Progress.start();
            this.$axios.get('api/set')
            .then(({data}) => {
                for(let x = 0; x < data.length; x++){
                    let hours = Math.floor((data[x].time / (60 * 60)) % 24);
                    let minutes = Math.floor((data[x].time / 60) % 60);

                    data[x].time = hours + ':' + ((minutes < 10) ? '0' + minutes : minutes);
                }
                this.setList = data;
                this.$Progress.finish();
            })
        },
        clearForm(){
            this.form.id = '';
            this.form.name = '';
            this.form.time = '';
            this.form.password = '';
        }
    },
    components: {
        TimePicker
    },
    created() {
        this.loadSets();
    }
}
</script>