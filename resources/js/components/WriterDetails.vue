<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-2">
                <router-link :to="{path:'/MyWriters'}" type="button"
                             class="btn btn-primary btn-sm">
                    <i class="fas fa-hand-o-left"></i>
                    Back
                </router-link>
                <div class="card">
                    <vue-tiny-tabs id="mytabs" :anchor="false" :closable="true" :hideTitle="false" @on-close="onClose" @on-before="onBefore" @on-after="onAfter">
                        <div class="section" id="example">
                            <h3 class="title">Finished Orders</h3>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Order#</th>
                                        <th>Title</th>
                                        <th>Deadline</th>
                                        <th>Level</th>
                                        <th>Status</th>
                                        <th>Urgency</th>
                                        <th>More</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="order in orders.data" :key="order.id">
                                        <td>{{order.order_number}}</td>
                                        <td>{{order.title}}</td>
                                        <td>
                                            <small style="color: red;">{{order.deadline|myDatetime}}</small>
                                        </td>
                                        <td>{{order.academic_level}}</td>
                                        <td>
                                    <span class="badge badge-pill badge-warning"
                                          v-if="order.status == 1">Pending..</span>
                                            <span class="badge badge-pill badge-info" v-if="order.status == 3">Uploaded</span>
                                            <span class="badge badge-pill badge-dark" v-if="order.status == 5">Completed</span>
                                            <span class="badge badge-pill badge-success" v-if="order.status == 6">Edited</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dark" v-if="order.urgency == 1">Urgent</span>
                                        </td>
                                        <td>
                                            <router-link :to="{path:'/orderdetails/'+ order.id}" type="button"
                                                         class="btn btn-primary btn-sm">More
                                            </router-link>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <pagination :data="orders" :limit="pages" @pagination-change-page="getResults"></pagination>
                            </div>
                        </div>
                        <div class="section" id="options">
                            <h3 class="title">Not Finished orders</h3>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Order#</th>
                                        <th>Title</th>
                                        <th>Deadline</th>
                                        <th>Level</th>
                                        <th>Status</th>
                                        <th>Urgency</th>
                                        <th>More</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="orderz in unorders.data" :key="orderz.id">
                                        <td>{{orderz.order_number}}</td>
                                        <td>{{orderz.title}}</td>
                                        <td>
                                            <small style="color: red;">{{orderz.deadline|myDatetime}}</small>
                                        </td>
                                        <td>{{orderz.academic_level}}</td>
                                        <td>
                                            <span class="badge badge-pill badge-warning" v-if="orderz.status == 1">Pending..</span>
                                            <span class="badge badge-pill badge-danger" v-if="orderz.status == 4">Revision</span>
                                            <span class="badge badge-pill badge-danger" v-if="orderz.status == 7">Cancelled</span>
                                            <span class="badge badge-pill badge-secondary" v-if="orderz.status == 8">Disputed</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dark" v-if="orderz.urgency == 1">Urgent</span>
                                        </td>
                                        <td>
                                            <router-link :to="{path:'/orderdetails/'+ orderz.id}" type="button"
                                                         class="btn btn-primary btn-sm">More
                                            </router-link>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <pagination :data="orders" :limit="pages" @pagination-change-page="getResults"></pagination>
                            </div>
                        </div>
                        <div class="section" id="reviews">
                            <h3 class="title">Writer's reviews</h3>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Order#</th>
                                        <th>Title</th>
                                        <th>Rating</th>
                                        <th>Details</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="review in reviews" :key="review.id">
                                        <td>{{review['orderId']}}</td>
                                        <td>{{review['title']}}</td>
                                        <td>
                                            <star-rating v-bind:increment="0.5" :read-only="true" v-bind:star-size="30" :rating="review['rate']" ></star-rating>
                                        </td>
                                        <td>
                                            <router-link :to="{path:'/orderdetails/'+ review['id']}" type="button"
                                                         class="btn btn-primary btn-sm">More
                                            </router-link>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="section" id="components">
                            <h3 class="title">Writer's Details</h3>
                           <div class="row">
                               <div class="col-md-4">Name</div>
                               <div class="col-md-4">{{users['name']}}</div>
                           </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">Email</div>
                                <div class="col-md-4">{{users['email']}}</div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">phone</div>
                                <div class="col-md-4">{{users['phone_number']}}</div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">Level</div>
                                <div class="col-md-4">
                                    <span class="badge badge-primary"  v-if="this.users['level_id'] == 2">Junior</span>
                                    <span class="badge badge-warning" v-if="this.users['level_id'] == 1">Starter</span>
                                    <span class="badge badge-dark" v-if="this.users['level_id'] == 3">Senior</span>
                                    <span class="badge badge-success" v-if="this.users['level_id'] == 4">Newbie</span>
                                    <span class="badge badge-default" v-if="this.users['level_id'] == 5">Mamba</span>
                                    <span class="badge badge-success" v-if="this.users['level_id'] == 6">Special</span>

                                   </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">Last login</div>
                                <div class="col-md-4"><span class="badge badge-warning">{{users['last_login'] | myDatetime }}</span></div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">Status</div>
                                <div class="col-md-4">
                                    <span class="badge badge-primary" v-if="users['status_id'] == 1">Active</span>
                                    <span class="badge badge-warning" v-if="users['status_id'] == 0">Pending</span>
                                    <span class="badge badge-danger" v-if="users['status_id'] == 2">Suspended</span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">Areas of specialities</div>
                                <div class="col-md-4">
                                    <span v-for="value in values" :key="value.id">
                                    <p class="badge badge-success">
                                        {{value}}
                                    </p>&nbsp;
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">Bank</div>
                                <div class="col-md-4">
                                    {{users['bank'] }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">Account number</div>
                                <div class="col-md-4">
                                    <h3 class="badge badge-danger">{{users['bank_account'] }}</h3>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </vue-tiny-tabs>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VueTinyTabs from 'vue-tiny-tabs'
    import Multiselect from 'vue-multiselect';
    export default {
        name: 'TinyTabs',
        components: {
            'vue-tiny-tabs': VueTinyTabs,
        },
        data(){
            return{
                rating: 1.5,
                orders:{},
                unorders:{},
                users:{},
                values:{},
                reviews:{},
                pages:10,
                userId:this.$route.params.userId,
            }
        },

        methods: {
            getResults(page = 1) {
                axios.get('/api/wrtorders?page=' + this.userId + page)
                    .then(response => {
                        this.orders = response.data;
                    });
            },
            getValues(){
                axios.get("/api/Myvalues/" + this.userId).then(({data}) => ([this.values = data['value']]));
            },
            getMyOrders(){
                axios.get("/api/wrtorders/" + this.userId ).then(({data}) => ([this.orders = data]));
            },
            getMyreviews(){
                axios.get("/api/reviews/" + this.userId ).then(({data}) => ([this.reviews = data['review']]));
            },
            loadUser() {
                axios.get("/api/ThisWriter/" + this.userId).then(({data}) => ([this.users = data]));
            },
            getProfilePhoto(img) {
                let photo = "img/profile/" + img;
                return photo;
            },
            getUnOrders(){
                axios.get("/api/unorders/" + this.userId ).then(({data}) => ([this.unorders = data]));
            },
            onClose (id) {
                console.log('Callback function that gets evaluated while closing the tab', id)
            },
            onBefore (id, tab) {
                console.log('Callback function that gets evaluated before a tab is activated', id, tab)
            },
            onAfter (id, tab) {
                console.log('Callback function that gets evaluated after a tab is activated', id, tab)
            }
        },
        created() {
            this.getValues();
            this.getMyOrders();
            this.getUnOrders();
            this.getMyreviews();
            this.loadUser();
        }
    }
</script>
<style>
    .tinytabs .tabs {
        margin-left: 15px;
        display: flex;
        flex-flow: row wrap;
    }
    .tinytabs .tabs .tab .close {
        padding-left: 5px;
    }
    .tinytabs .tabs .tab {
        margin: 0 3px 0 0;
        background: #e1e1e1;
        display: block;
        padding: 6px 15px;
        text-decoration: none;
        color: #666;
        font-weight: bold;
        border-radius: 3px 3px 0 0;
    }
    .tinytabs .section {
        background: #f1f1f1;
        overflow: hidden;
        padding: 15px;
        clear: both;
        border-radius: 3px;
    }
    .tinytabs .tab.sel {
        background: #f1f1f1;
        color: #333;
        text-shadow: none;
    }


</style>
