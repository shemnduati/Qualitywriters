<template>
    <div class="container-fluid">
        <div class="row justify-content-center pl-0 pr-0 ml-0 mr-0">
            <div class="col-md-12 mt-4 pl-0 pr-0 ml-0 mr-0">
                <div class="card pl-0 pr-0 ml-0 mr-0">
                    <div class="card-header">
                        <h3 class="card-title">Disputes</h3>

                        <div class="card-tools">
                            <router-link :to="{path:'/orderdetails/'+ orderId}" type="button"
                                         class="btn btn-primary btn-sm">
                                <i class="fas fa-hand-o-left"></i>
                                Back
                            </router-link>
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover" v-if="disputes">
                            <thead>
                            <tr>
                                <th>Order#</th>
                                <th>Datetime</th>
                                <th>Instruction</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="dis in disputes.data" :key="dis.id">
                                <td>{{dis.order_number}}</td>
                                <td>{{dis.created_at | myDatetime}}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-dark" @click="viewInstruction(dis.instruction)">View</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <p v-if="!disputes">There are no disputes</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="instModal" tabindex="-1" role="dialog" aria-labelledby="addnewLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Instruction</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body">
                            <div v-html="instruction"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Revisions",
        data(){
            return{
                orderId: this.$route.params.orderId,
                disputes: {},
                instruction: ''
            }
        },
        methods:{
            viewInstruction(inst){
                this.instruction = inst;
                $('#instModal').modal('show');
            },
            getRevision(){
                axios.get("/api/disputes/" + this.orderId).then(({data}) => ([this.disputes = data]));
            }
        },
        created() {
            this.getRevision();
        }
    }
</script>

<style scoped>

</style>
