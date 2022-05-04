<template>
    <div class="container" v-if="$gate.isAdmin()">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Editor Categories</h3>

                        <div class="card-tools">
                            <button class="btn btn-sm btn-primary" @click="newModal">Add Category</button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4" v-for="cat in categories.data" :key="cat.id">
                                <div class="info-box mb-3 bg-success">
                                    <span class="info-box-icon"><i class="fas fa-2x fa-chalkboard-teacher"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">{{cat.title}}</span>
                                        <span class="info-box-number">Ksh. {{cat.amount}}</span>
                                    </div>
                                    <span class="info-box-icon" @click="editCategory(cat)"><i
                                            class="fas fa-edit"></i></span>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="addnewLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" v-if="!this.editMode">Add Category of Editor(s)</h5>
                        <h5 class="modal-title" v-if="this.editMode">Edit Category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Title</label>
                                <input v-model="form.title" type="text" name="name"
                                       placeholder="Enter Title"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                                <has-error :form="form" field="title"></has-error>
                            </div>
                            <div class="form-group">
                                <label>Amount Per Page</label>
                                <input v-model="form.amount" type="number" name="amount"
                                       placeholder="Amount Per Page"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('amount') }">
                                <has-error :form="form" field="amount"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" @click="addCategory" v-if="!this.editMode">
                                Create
                            </button>
                            <button type="button" class="btn btn-success" @click="updateCategory" v-if="this.editMode">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row mt-5">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Editors</h3>
            </div>
            <!-- /.box-header -->
            <div class="card-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone No.</th>
                  <th>level</th>
                  <th>Assign Levels</th>
                </tr>
                 <!-- v-for="user in users" :key="user.id" -->
                <tr v-for="editor in editors" :key="editor.id">
                  <td>{{editor.id}}</td>
                  <td>{{editor.name}}</td>
                  <td>{{editor.email}}</td>
                  <td>{{editor.phone}}</td>
                  <td>
                       <span class="badge badge-danger" v-if="editor.title == null">Not Assigned</span>
                        <span class="badge badge-success" v-if="editor.title != null"> {{editor.title}}</span>
                     </td>
                  <td>
                      <button class="btn btn-primary btn-sm" @click="assign(editor)">
                          <i class="fa fa-edit"></i>
                      </button>
                  </td>
                </tr>
              </tbody></table>
            </div>
            <!-- /.box-body -->
              <div class="card-footer">
                  <pagination :data="editors" limit="10" @pagination-change-page="getResults"></pagination>
              </div>
          </div>
          <!-- /.box -->
        </div>
        </div>
         <!-- Modal -->
      <div class="modal fade" id="Assign" tabindex="-1" role="dialog" aria-labelledby="AddNewLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="AddNewUser">Assign Editor Level</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form @submit.prevent="assignLevel">
              <div class="modal-body">
                <div class="form-group">
                  <select v-model="formb.editor_level_id" class="form-control" name="editor_level_id" id="editor_level_id"
                          :class="{ 'is-invalid': form.errors.has('editor_level_id') }">
                      <option selected value="">--Select Level--</option>
                      <option v-for="category in categories.data" :value="category.id" :key="category.id">{{ category.title }}</option>
                  </select>
                  <has-error :form="form" field="editor_level_id"></has-error>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Assign level</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
    export default {
        name: "EditorCategory",
        data() {
            return {
                categories: '',
                editMode: '',
                editors:{},
                form: new Form({
                    id: '',
                    title: '',
                    amount: '',
                }),
                formb: new Form({
                   id: '',
                   editor_level_id: '',
                }),
            }
        },
        methods: {
            updateCategory() {
                this.form.put('api/editorCategory/' + this.form.id)
                    .then(() => {
                        $('#addnew').modal('hide');
                        Swal.fire({
                            title: 'Updated!',
                            text: 'The Category has been updated.',
                            type: 'success'
                        })
                        Fire.$emit('entry');
                    })
                    .catch(() => {

                    })
            },
            assignLevel() {
                this.formb.put('api/assignLevel/' + this.formb.id)
                    .then(() => {
                        $('#Assign').modal('hide');
                        Swal.fire({
                            title: 'Assigned!',
                            text: 'The Editors Assigned Level.',
                            type: 'success'
                        })
                         Fire.$emit('entry');
                        
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                        Swal.fire({
                            type: 'error',
                            title: 'Error!!',
                            text: error.response.data.msg,

                        })
                    })
            },
             assign(editor) {
                this.formb.id = editor.id;
              $('#Assign').modal('show');
              this.formb.fill(editor);
            },
            editCategory(cat) {
                this.editMode = true;
                $('#addnew').modal('show');
                this.form.fill(cat);
            },
            getCategories() {
                axios.get("/api/editorCategory").then(({data}) => ([this.categories = data]));
            },
            getEditors(){
                axios.get("api/editors").then(({ data }) => (this.editors = data['editors']));
            },
            addCategory() {
                this.form.post('api/editorCategory')
                    .then(() => {
                        Fire.$emit('entry');
                        toast.fire({
                            type: 'success',
                            title: 'EditorCategory created successfully'
                        });
                        this.form.reset();
                        $('#addnew').modal('hide');
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                        Swal.fire({
                            type: 'error',
                            title: 'Error!!',
                            text: error.response.data.msg,

                        })
                    })
            },
            newModal() {
                this.editMode = false;
                this.form.reset();
                $('#addnew').modal('show');
            },
        }
        ,
        created() {
            this.getCategories();
            this.getEditors();
            Fire.$on('entry', () => {
                this.getCategories();
                 this.getEditors();
            })
        }
    }
</script>

<style scoped>

</style>