<template>
    <div class="container-fluid pl-0 pr-0 ml-0 mr-0" v-if="$gate.isAdminOrisEditor()">
        <div class="row justify-content-center pl-0 pr-0 ml-0 mr-0">
            <div class="col-md-12 mt-4 pl-0 pr-0 ml-0 mr-0">
                <div class="card pl-0 pr-0 ml-0 mr-0">
                    <div class="card-header">
                        <h3 class="card-title">Orders</h3>

                        <div class="card-tools">
                            <button class="btn btn-sm btn-secondary" @click="adminSort(3)" v-if="$gate.isAdmin()">Uploaded
                            </button>
                            <button class="btn btn-sm btn-danger" @click="adminSort(4)" v-if="$gate.isAdmin()">Revision
                            </button>
                            <button class="btn btn-sm btn-dark" @click="adminSort(5)" v-if="$gate.isAdmin()">Completed
                            </button>
                            <button class="btn btn-sm btn-info" @click="adminSort(6)" v-if="$gate.isAdmin()">Edited
                            </button>
                            <button class="btn btn-sm btn-warning" @click="adminSort(1)" v-if="$gate.isAdmin()">assigned
                            </button>
                            <button class="btn btn-sm btn-success" @click="adminSort(0)" v-if="$gate.isAdmin()">Unassigned
                            </button>
                            <button class="btn btn-sm btn-primary" @click="newModal" v-if="$gate.isAdmin()">New Order
                            </button>
                            <button class="btn btn-sm btn-primary" @click="editorSort(3)" v-if="$gate.isEditor()">Pending
                            </button>
                            <button class="btn btn-sm btn-warning" @click="editorSort(4)" v-if="$gate.isEditor()">On
                                Revision
                            </button>

                            <button class="btn btn-sm btn-success" @click="editorSort(6)" v-if="$gate.isEditor()">Edited
                            </button>
                            <button class="btn btn-sm btn-dark" @click="editorSort(5)" v-if="$gate.isEditor()">
                                Completed
                            </button>
                        </div>
                    </div>

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
                                <th v-if="$gate.isAdmin()">Action</th>
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
                                          v-if="order.status == 0">Pending..</span>
                                    <span class="badge badge-pill badge-info" v-if="order.status == 1">Assigned</span>
                                    <span class="badge badge-pill badge-primary"
                                          v-if="order.status == 3">Uploaded</span>
                                    <span class="badge badge-pill badge-danger" v-if="order.status == 4">Revision</span>
                                    <span class="badge badge-pill badge-dark" v-if="order.status == 5">Completed</span>
                                    <span class="badge badge-pill badge-success" v-if="order.status == 6">Edited</span>
                                    <span class="badge badge-pill badge-danger" v-if="order.status == 7">Cancelled</span>
                                    <span class="badge badge-pill badge-secondary" v-if="order.status == 8">Disputed</span>
                                </td>
                                <td>
                                    <span class="badge badge-dark" v-if="order.urgency == 1">Urgent</span>
                                </td>
                                <td v-if="$gate.isAdmin()">
                                    <i class="fas fa-window-close" style="color:orange; cursor: pointer;" @click="cancelOrder(order.id)"></i>
                                    <i class="fas fa-trash" style="color: red; cursor: pointer;" @click="deleteOrder(order.id)"></i>
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
            </div>
        </div>
        <div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="addnewLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addnewLabel">Post Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="submitOrder()">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input v-model="form.title" type="text" class="form-control" name="title"
                                               id="title"
                                               placeholder="Title"
                                               :class="{ 'is-invalid': form.errors.has('title') }">
                                        <has-error :form="form" field="title"></has-error>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="title">Order Number</label>
                                        <input v-model="form.order_number" type="text" min="1" class="form-control"
                                               name="order_number"
                                               id="order_number"
                                               placeholder="Order Number"
                                               :class="{ 'is-invalid': form.errors.has('order_number') }">
                                        <has-error :form="form" field="order_number"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="title">Discipline</label>
                                        <input v-model="form.discipline" type="text" class="form-control"
                                               name="discipline"
                                               id="discipline"
                                               placeholder="Discipline"
                                               :class="{ 'is-invalid': form.errors.has('discipline') }">
                                        <has-error :form="form" field="discipline"></has-error>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="level">Level</label>
                                        <select v-model="form.level" class="form-control" name="level" id="level"
                                                :class="{ 'is-invalid': form.errors.has('level') }">
                                            <option selected value="">--Select Level--</option>
                                            <option value="College">College</option>
                                            <option value="High School">High School</option>
                                            <option value="Masters">Masters</option>
                                            <option value="PhD">PhD</option>
                                            <option value="University">University</option>
                                        </select>
                                        <has-error :form="form" field="level"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>No. of words</label><br>
                                        <input v-model="form.words" type="number" min="1" class="form-control"
                                               name="words"
                                               id="words"
                                               placeholder="Number of words"
                                               :class="{ 'is-invalid': form.errors.has('words') }">
                                        <has-error :form="form" field="words"></has-error>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="deadline">Deadline Date & Time</label>
                                        <datetime type="datetime" :auto="true" :min-datetime="this.now" zone="local"
                                                  value-zone="UTC+3" v-model="form.deadline"
                                                  id="deadline"></datetime>
                                        <p style="color: red;" v-if="this.e_deadline">{{this.e_deadline}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="spacing">Spacing</label><br>
                                    <radio name="spacing" value="single" id="spacing" v-model="form.spacing">
                                        Single
                                    </radio>
                                    <radio name="spacing" value="double" id="spacing" v-model="form.spacing">
                                        Double
                                    </radio>
                                    <p style="color: red;">{{this.e_spacing}}</p>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="format">Paper Format</label>
                                        <input v-model="form.paper_format" type="text" class="form-control"
                                               name="format"
                                               id="format"
                                               placeholder="Format"
                                               :class="{ 'is-invalid': form.errors.has('paper_format') }">
                                        <has-error :form="form" field="paper_format"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="Sources">N.o of sources</label>
                                        <input v-model="form.sources" type="number" min="1" class="form-control"
                                               name="sources"
                                               id="sources"
                                               placeholder="Number of  Sources"
                                               :class="{ 'is-invalid': form.errors.has('sources') }">
                                        <has-error :form="form" field="sources"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group" style="padding: 20px">
                                    <label for="description">Description</label>
                                    <vue-editor v-model="form.description" id="description"></vue-editor>
                                    <p style="color: red;">{{this.e_description}}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <label for="description">Toggle if Urgent</label><br>
                                    <toggle-button :value="this.urgent" @change="isUrgent()"/>
                                </div>
                                <div class="col">
                                    <div class="form-group"  v-if="this.urgent">
                                        <label for="title">Amount Per word in Ksh</label>
                                        <input v-model="form.amount" type="number" min="1" class="form-control"
                                               name="amount" id="amount"
                                               placeholder="Amount"
                                               :class="{ 'is-invalid': form.errors.has('amount') }">
                                        <has-error :form="form" field="amount"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group justify-content-center">
                                        <label for="files">Upload Files</label>
                                        <input type="file" multiple class="form-control-file" @change="fieldChange"
                                               id="files">
                                        <has-error :form="form" field="files"></has-error>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row justify-content-center">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <v-select id="writerSelect" v-model="writer_obj" label="email" :options="writers"
                                                  placeholder="Assign Writer" v-on:change="writerId()"></v-select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success btn-submit" @click="validateForm" :disabled="btn_submit">Create</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {VueEditor} from "vue2-editor";
    import 'vue-datetime/dist/vue-datetime.css';
    import 'vue-select/dist/vue-select.css';


    export default {
        name: "AdminOrders",
        components: {
            VueEditor
        },
        data() {
            return {
                btn_submit: false,
                message: '',
                typing: '',
                pages:1,
                users: {},
                messages: [],
                orderId: this.$route.params.orderId,
                now: moment().format(),
                e_deadline: '',
                e_spacing: '',
                e_description: '',
                e_viewers: '',
                writers: {},
                orders: {},
                writer_obj: '',
                urgent: false,
                attachments: [],
                categories: {},
                formf: new FormData(),
                form: new Form({
                    urgent: 0,
                    amount: '',
                    pages: 1,
                    title: '',
                    order_number: '',
                    discipline: '',
                    level: '',
                    deadline: '',
                    files: '',
                    spacing: '',
                    paper_format: '',
                    description: '',
                    writer: '',
                    sources: '',
                    words:'',
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('api/order?page=' + page)
                    .then(response => {
                        this.orders = response.data;
                    });
            },
            deleteOrder(orderId){
                if(this.$gate.isAdmin()) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Delete this Order??",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.value) {
                            axios.delete("/api/delete_order/" + orderId).then(() => {
                                Fire.$emit('entry');
                                Swal.fire(
                                    'Deleted!',
                                    'Successfully Deleted!!',
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
            cancelOrder(orderId){
                if(this.$gate.isAdmin()) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Cancel this Order??",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Cancel it!'
                    }).then((result) => {
                        if (result.value) {
                            axios.delete("/api/cancel_order/" + orderId).then(() => {
                                Fire.$emit('entry');
                                Swal.fire(
                                    'Cancel!',
                                    'Canceled!!',
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
            getCategories() {
                axios.get("/api/category").then(({data}) => ([this.categories = data]));
            },
            adminSort(sort){
              if (this.$gate.isAdmin()){
                  axios.get("/api/adminsort/" + sort).then(({data}) => ([this.orders = data]));
              }
            },
            editorSort(sort){
                if (this.$gate.isEditor()){
                    axios.get("/api/editorsort/" + sort).then(({data}) => ([this.orders = data]));
                }
            },
            writerId() {
                if (this.writer_obj) {
                    this.form.writer = this.writer_obj['id'];
                } else if (!this.writer_obj) {
                    this.form.writer = '';
                }
            },
            getWriters() {
                axios.get("/api/getwriters").then(({data}) => ([this.writers = data]));
            },
            getOrders() {
               axios.get("/api/order").then(({data}) => ([this.orders = data]));
            },
            validateForm() {
                this.writerId();
                if (!this.form.title) {
                    // set(type, 'required');
                    this.form.errors.set({
                        title: 'This field is required'
                    })
                    return false;
                } else if (!this.form.order_number) {
                    this.form.errors.set({
                        order_number: 'This field is required'
                    })
                    return false;
                } else if (!this.form.discipline) {
                    this.form.errors.set({
                        discipline: 'This field is required'
                    })
                    return false;
                } else if (!this.form.level) {
                    this.form.errors.set({
                        level: 'This field is required'
                    })
                    return false;
                } else if (!this.form.words) {
                    this.form.errors.set({
                        words: 'This field is required'
                    })
                    return false;
                } else if (!this.form.deadline) {
                    this.e_deadline = 'This field is required';
                    return false;
                } else if (!this.form.spacing) {
                    this.e_deadline = '';
                    this.e_spacing = 'This field is required';
                    return false;
                } else if (!this.form.paper_format) {
                    this.e_spacing = '';
                    this.form.errors.set({
                        paper_format: 'This field is required'
                    })
                    return false;
                } else if (!this.form.sources) {
                    this.form.errors.set({
                        sources: 'This field is required'
                    })
                    return false;
                } else if (!this.form.description) {
                    this.e_description = 'This field is required';
                    return false;
                } else {
                    if (this.form.urgent == 1) {
                        if (!this.form.amount) {
                            this.form.errors.set({
                                amount: 'This field is required'
                            })
                            return false;
                        } else {
                            this.submitOrder();
                        }
                    } else {
                        this.submitOrder();
                    }
                }
            },
            submitOrder() {
                this.btn_submit = true;
                for (let i = 0; i < this.attachments.length; i++) {
                    this.formf.append('files[]', this.attachments[i]);
                }
                this.formf.append('title', this.form.title);
                this.formf.append('order_number', this.form.order_number);
                this.formf.append('discipline', this.form.discipline);
                this.formf.append('level', this.form.level);
                this.formf.append('words', this.form.words);
                this.formf.append('deadline', moment(this.form.deadline).format('YYYY-MM-DD HH:mm:ss'));
                this.formf.append('spacing', this.form.spacing);
                this.formf.append('paper_format', this.form.paper_format);
                this.formf.append('sources', this.form.sources);
                this.formf.append('description', this.form.description);
                this.formf.append('urgent', this.form.urgent);
                this.formf.append('amount', this.form.amount);
                this.formf.append('writer', this.form.writer);

                const config = {headers: {'Content-Type': 'multipart/form-data'}};

                axios.post('/api/order', this.formf, config).then(response => {
                    $('#addnew').modal('hide');
                    Fire.$emit('entry');
                    this.form.reset();
                    Swal.fire({
                        type: 'success',
                        title: 'Submited!!',
                        text: 'Successfully',

                    })
                    this.btn_submit = false;
                    location.reload();

                })
                    .catch(error => {
                        this.btn_submit = false;
                        this.errors = error.response.data.errors;
                        Swal.fire({
                            type: 'error',
                            title: 'Error!!',
                            text: error.response.data.msg,

                        })
                    });
            },
            fieldChange(e) {
                let selectedFiles = e.target.files;
                if (!selectedFiles.length) {
                    return false;
                }
                for (let i = 0; i < selectedFiles.length; i++) {
                    this.attachments.push(selectedFiles[i]);
                }
                // console.log(this.attachments);
            },
            isUrgent() {
                if (!this.urgent) {
                    this.urgent = true;
                    this.form.urgent = 1;
                } else {
                    this.urgent = false;
                    this.form.urgent = 0;
                }
            },
            newModal() {
                $("#files").val('');
                $("#writerSelect").val('');
                this.form.reset();
                this.attachments = [];
                $('#addnew').modal('show');
            },
        },
        created() {
            Fire.$on('searching', ()=>{
                let query = this.$parent.search;
                axios.get('api/findOrder?q=' + query)
                    .then((data)=>{
                        this.orders = data.data;
                    })
                    .catch(()=>{

                    })
            });
            this.getOrders();
            this.getWriters();
            this.getCategories();
            Fire.$on('entry', () => {
                this.getOrders();
            })
        }
    }
</script>

<style></style>
