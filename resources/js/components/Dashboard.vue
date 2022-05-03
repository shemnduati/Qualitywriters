<template>
    <div class="container">
        <div class="row justify-content-center" v-if="$gate.isAdminOrisEditor()">
            <div class="col-md-12">
                <div class="row mt-5">
                    <div class="col-lg-3 col-6" v-if="$gate.isEditor()">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{dash['pending']}}</h3>

                                <p>Pending Editing</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-hourglass-start white"></i>
                            </div>
                            <router-link to="/order" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6" v-if="$gate.isAdmin()">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{dash['total']}}</h3>

                                <p>Total orders</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clipboard-list white"></i>
                            </div>
                            <router-link to="/order" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6" v-if="$gate.isAdmin()">
                        <!-- small box -->
                        <div class="small-box bg-dark">
                            <div class="inner">
                                <h3>{{dash['active']}}</h3>

                                <span>Active orders</span><br>
                                <span>Unassigned</span>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock white"></i>
                            </div>
                            <router-link to="/order" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6" v-if="$gate.isAdmin()">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{dash['edited']}}</h3>

                                <p>Edited orders</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-edit white"></i>
                            </div>
                            <router-link to="/order" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6" v-if="$gate.isEditor() || $gate.isAdmin()">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{dash['completed']}}</h3>

                                <p>Completed orders</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clipboard-check white"></i>
                            </div>
                            <router-link to="/order" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
                        </div>
                    </div>
                     
                    <!-- ./col -->
                    <div class="col-lg-3 col-6" v-if="$gate.isEditor() || $gate.isAdmin()">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{dash['revision']}}</h3>

                                <p>Revisions</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-undo white"></i>
                            </div>
                            <router-link to="/order" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6" v-if="$gate.isEditor()">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h4>Ksh.{{Math.trunc(walletBalance.amount)}}</h4>

                                <p>My Wallet</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-wallet white"></i>
                            </div>
                            <router-link to="/walletTransactions" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6" v-if="$gate.isAdmin()">
                        <!-- small box -->
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{dash['totalOwed'].toFixed(2)}}</h3>

                                <p>Total Owed (Ksh.)</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-dollar white"></i>
                            </div>
                            <router-link to="/earnings" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
            </div>
        </div>
        <div class="row justify-content-center" v-if="$gate.isWriter()">
            <div class="col-md-12">
                <div class="row mt-5">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h4>Ksh.{{Math.trunc(walletBalance.amount)}}</h4>

                                <p>My Wallet</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-wallet white"></i>
                            </div>
                            <router-link to="/walletTransactions" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h4>{{dash['new']}}</h4>

                                <p>New orders</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clipboard-list white"></i>
                            </div>
                            <router-link to="/myorder" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
                        </div>
                    </div>
                    <!-- ./col -->
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h4>{{dash['active']}}</h4>

                                <p>Active orders</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clipboard-list white"></i>
                            </div>
                            <router-link to="/myorder" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h4>{{dash['completed']}}</h4>

                                <p>Completed orders</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clipboard-check white"></i>
                            </div>
                            <router-link to="/myorder" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h4>{{dash['revision']}}</h4>

                                <p>Revisions</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-undo white"></i>
                            </div>
                            <router-link to="/myorder" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
                        </div>
                    </div>
                    <!-- ./col -->

                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-dark">
                            <div class="inner">
                                <h4>{{dash['edited']}}</h4>

                                <p>Edited</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-edit white"></i>
                            </div>
                            <router-link to="/myorder" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props:{
            user: {
                type: Object,
                required: true
            }
        },
        data(){
            return{
                walletBalance: '',
                dash: {}
            }
        },
        methods :{
            getDashboard(){
                if(this.$gate.isAdminOrisEditor()) {
                    axios.get("api/dashboard").then(({data}) => ([this.dash = data['data']]));
                }
                if(this.$gate.isWriter()){
                    axios.get("api/myDashboard/"+ this.user.id).then(({data})=>([this.dash = data['data']]));
                }
            },
            getWalletBalance(){
                if (this.$gate.isWriter()) {
                    axios.get('api/wallet/').then(({data}) => ([this.walletBalance = data]));
                }
            }
        },
        created() {
            this.getWalletBalance();
            this.getDashboard();
        }
    }
</script>
