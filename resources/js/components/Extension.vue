<template>
    <div class="container-fluid">
        <div class="row justify-content-center pl-0 pr-0 ml-0 mr-0">
            <div class="col-md-12 mt-4 pl-0 pr-0 ml-0 mr-0">
                <div class="card pl-0 pr-0 ml-0 mr-0">
                    <div class="card-header">
                        <h3 class="card-title">Revisions</h3>

                        <div class="card-tools">
                            <router-link :to="{path:'/orderdetails/'+ orderId}" type="button"
                                         class="btn btn-primary btn-sm">
                                <i class="fas fa-hand-o-left"></i>
                                Back
                            </router-link>
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Order#</th>
                                <th>Initial</th>
                                <th>Requested</th>
                                <th>Requested At</th>
                                <th>Status</th>
                                <th v-if="$gate.isAdmin()">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="ext in extensions.data" :key="ext.id">
                                <td>{{ext.order_number}}</td>
                                <td>{{ext.initial | myDatetime}}</td>
                                <td>{{ext.deadline | myDatetime}}</td>
                                <td>{{ext.created_at | myDatetime}}</td>
                                <td>
                                    <span class="badge badge-primary" v-if="ext.status == 0">pending</span>
                                    <span class="badge badge-success" v-if="ext.status == 1">accepted</span>
                                    <span class="badge badge-danger" v-if="ext.status == 2">rejected</span>
                                </td>
                                <td v-if="$gate.isAdmin()">
                                    <button class="btn btn-primary btn-sm" @click="makeAction(1, ext.id)" v-if="ext.status != 1">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                    &nbsp;
                                    <button type="button" class="btn btn-danger btn-sm" @click="makeAction(2, ext.id)" v-if="ext.status != 2">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="instModal" tabindex="-1" role="dialog" aria-labelledby="addnewLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Instruction</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{instruction}}
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
        name: "Extension",
        data() {
            return {
                orderId: this.$route.params.orderId,
                extensions: {}
            }
        },
        methods: {
            makeAction(act, extId) {
                if (this.$gate.isAdmin()) {
                      if(act == 1){
                          Swal.fire({
                              title: 'Are you sure?',
                              text: "Accept this request??",
                              type: 'warning',
                              showCancelButton: true,
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText: 'Yes, delete it!'
                          }).then((result) => {
                              if (result.value) {
                                  axios.put("/api/accept_request/" + extId).then(() => {
                                      Fire.$emit('entry');
                                      Swal.fire(
                                          'Accepted!',
                                          'Extension request Accepted!!',
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

                      if (act==2){
                          Swal.fire({
                              title: 'Are you sure?',
                              text: "Decline this request??",
                              type: 'warning',
                              showCancelButton: true,
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText: 'Yes, decline it!'
                          }).then((result) => {
                              if (result.value) {
                                  axios.put("/api/decline_request/" + extId).then(() => {
                                      Fire.$emit('entry');
                                      Swal.fire(
                                          'Decline!',
                                          'Successfully Declined!!',
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
                }
            },
            getRequests() {
                axios.get("/api/isExtension/" + this.orderId).then(({data}) => ([this.extensions = data]));
            }
        },
        created() {
            this.getRequests();
            Fire.$on('entry', () => {
                this.getRequests();
            })
        }
    }
</script>

<style scoped>

</style>
