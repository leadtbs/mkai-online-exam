<template>
    <div>
        <SetCardHeader />
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mt-2 p-0">
                    <table class="text-center table-bordered m-auto" style="width: 95%;">
                        <tr>
                            <td style="height: 50px;" colspan="5" class="align-middle font-weight-bold set-pick">Set 1</td>
                        </tr>
                        <tr>
                            <td class="align-middle" style="width:20%"><font-awesome-icon class="fa-fw text-info" icon="clock" />1:00</td>
                            <td class="align-middle" style="width:15%"><font-awesome-icon class="fa-fw text-success" icon="question" />5</td>
                            <td class="align-middle" style="width:15%"><font-awesome-icon class="fa-fw text-success" icon="star" />5</td>
                            <td class="align-middle" style="width:10%">
                                <b-button variant="primary" size="xs"><font-awesome-icon icon="edit" /></b-button>
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
            @ok="submitForm">

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

        </b-modal>
    </div>
</template>

<script>
import SetCardHeader from '@/components/Admin/SetCardHeader'
import TimePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import jQuery from 'jquery'

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
            form: {
                id: '',
                name: '',
                time: '',
                password: ''
            }
        }
    },
    methods: {
        submitForm(){
            console.log('mao ni');
        }
    },
    components: {
        SetCardHeader,
        TimePicker
    },
}
</script>