<template>
    <div>
        <jltexam v-if="set_type == 'jlt'"></jltexam>
        <ncexam v-if="set_type != 'jlt'"></ncexam>
    </div>
</template>

<script>
import JLTExam from '@/components/JLTExam'
import NCExam from '@/components/NCExam'

export default {
    name: 'exam',
    components: {
        'jltexam': JLTExam,
        'ncexam': NCExam,
    },
    data() {
        return {
            set_type: this.$route.params.set_type
        }
    }
}
</script>

<style>
    .picked {
        outline-style: solid;
        outline-color: blue;
    }
    .clickable {
        cursor: pointer;
    }
    .tempImage{
        display: inline-block;
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        height: 500px;
    }
    .camera {
        width: 80%;
        max-height: 500px;
        margin: 0 auto;
    }
    #feed, .picture{
        display: block;
        width: 80%;
        max-height: 300px;
        margin: 0 auto;
        background-color: #171717;
        box-shadow: 5px 5px 12px 0px rgba(0,0,0, 0.25);
    }
    #canvas{
        display: block;
        width: 100%;
        max-height: 300px;
        margin: 0 auto;
    }
    .sticky-div{
        position: -webkit-sticky; /* Safari & IE */
        position: sticky;
        top: 20px;
    }
</style>