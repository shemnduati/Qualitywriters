<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mt-5">
                    <div class="card-header">Payment Report
                    <div class="card-tools">
                        <router-link :to="{path:'/earnings'}" type="button"
                                     class="btn btn-primary btn-sm">
                            <i class="fas fa-hand-o-left"></i>
                            Back
                        </router-link>

                    </div>
                    </div>
                    <div class="card-body">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>NO.</th>
                                    <th>Date</th>
                                    <th>Order#</th>
                                    <th>Type</th>
                                    <th>Percentage</th>
                                    <th>Amount</th>
                                    <th>Balance</th>
                                    <th>Reason</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="trans in transactions.data" :key="trans.id">
                                    <td>{{trans.id}}</td>
                                    <td>{{trans.created_at|myDatetime}}</td>
                                    <td>
                                        <router-link :to="{path:'/orderdetails/'+ trans.order_id}">#{{trans.order_number}}
                                        </router-link>
                                    </td>
                                    <td>
                                        <span class="badge badge-pill badge-danger" v-if="trans.type == 1">Fine</span>
                                        <span class="badge badge-pill badge-info" v-if="trans.type == 3">PayOut</span>
                                        <span class="badge badge-pill badge-success" v-if="trans.type == 0">Awarded</span>
                                        <span class="badge badge-pill badge-warning" v-if="trans.type == 4">Bonus</span>
                                        <span class="badge badge-pill badge-secondary" v-if="trans.type == 6">Cancelled</span>
                                        <span class="badge badge-pill badge-dark" v-if="trans.type == 5">Fine Cancelled</span>
                                        <span class="badge badge-pill badge-secondary" v-if="trans.type == 8">Disputed</span>
                                        <span class="badge badge-pill badge-secondary" v-if="trans.type == 9">Resolved</span>
                                        <span class="badge badge-pill badge-danger" v-if="trans.type == 7">cancelled</span>
                                    </td>
                                    <td><span v-if="trans.type == 1 || trans.type == 5">{{Math.trunc(trans.percentage)}}%</span></td>
                                    <td>
                                        Ksh. {{trans.amount}}
                                    </td>
                                    <td>
                                        Ksh. {{trans.balance}}
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" v-if="trans.type == 1 || trans.type == 4 || trans.type == 8" @click="reasonModal(trans.description)">Reason</button>
                                        <button type="button" class="btn btn-success btn-sm" v-if="trans.type == 1" @click="cancelFine(trans.id)">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm" v-if="trans.type == 8" @click="acceptOrder(trans.id)">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" v-if="trans.type == 8" @click="cancelOrder(trans.id)">
                                             <i class="fas fa-times-circle"></i>
                                        </button>


                                        <button type="button" class="btn btn-dark btn-sm" v-if="trans.type == 5 || trans.type == 9" @click="reasonModal(trans.description)">Details</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <pagination :data="transactions"  :limit="pages" @pagination-change-page="getResults"></pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="reasonModal" tabindex="-1" role="dialog" aria-labelledby="addnewLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addnewLabel">Reason</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>{{reason}}</p>
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
        data(){
            return{
                userId : this.$route.params.userId,
                transactions:{},
                reason: '',
                pages:5,
            }
        },
        methods : {
            getResults(page = 1) {
                axios.get('/api/myearnings/' + this.userId +'?page=' + page)
                    .then(response => {
                        this.transactions = response.data;
                    });
            },
            cancelFine(fineId){
              if (this.$gate.isAdmin()){
                  Swal.fire({
                      title: 'Are you sure?',
                      text: "Drop this fine??",
                      type: 'warning',
                      showCancelButton: true,
                      confirmButtonColor: '#3085d6',
                      cancelButtonColor: '#d33',
                      confirmButtonText: 'Yes, drop it!'
                  }).then((result) => {
                      if (result.value) {
                          axios.post("/api/drop_fine/" + fineId).then(() => {
                              Fire.$emit('entry');
                              Swal.fire(
                                  'Dropped!',
                                  'Fine dropped!!',
                                  'success'
                              )
                              Fire.$emit('entry');
                          }).catch(error => {
                              this.errors = error.response.data.errors;
                              Swal.fire({
                                  type: 'error',
                                  title: 'Error!!',
                                  text: error.response.data.msg,
                              })
                          });
                      }
                  })
              }
            },
            cancelOrder(DisputId){
                if (this.$gate.isAdmin()){
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Cancel this Order",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, cancel it!'
                    }).then((result) => {
                        if (result.value) {
                            axios.post("/api/cancel_order/" + DisputId).then(() => {
                                Fire.$emit('entry');
                                Swal.fire(
                                    'Canceled!',
                                    'Order Canceled!!',
                                    'success'
                                )
                                Fire.$emit('entry');
                            }).catch(error => {
                                this.errors = error.response.data.errors;
                                Swal.fire({
                                    type: 'error',
                                    title: 'Error!!',
                                    text: error.response.data.msg,
                                })
                            });
                        }
                    })
                }
            },
            acceptOrder(DisputId){
                if (this.$gate.isAdmin()){
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Accept this as completed Order",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Accept it!'
                    }).then((result) => {
                        if (result.value) {
                            axios.post("/api/accept_order/" + DisputId).then(() => {
                                Fire.$emit('entry');
                                Swal.fire(
                                    'Accepted!',
                                    'Order Accepted!!',
                                    'success'
                                )
                                Fire.$emit('entry');
                            }).catch(error => {
                                this.errors = error.response.data.errors;
                                Swal.fire({
                                    type: 'error',
                                    title: 'Error!!',
                                    text: error.response.data.msg,
                                })
                            });
                        }
                    })
                }
            },
            reasonModal(reason){
                this.reason = reason;
                $('#reasonModal').modal('show');
            },
            getMyEarning(){
                axios.get("/api/myearnings/" + this.userId).then(({data})=>([this.transactions = data]));
            },
        },
        created() {
           this. getMyEarning();
            Fire.$on('entry', () => {
                this.getMyEarning();
            })
        }
    }
</script>
