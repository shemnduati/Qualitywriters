<template>
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Order Details<span class="badge badge-dark" style="margin-left: 4px;"
                                                                  v-if="details.urgency == 1">Urgent</span></h3>
                        <div class="card-tools">
                            <router-link :to="{path:'/revision/'+ orderId}">
                                <button type="button" class="btn btn-danger btn-sm">
                                    <i class="fas fa-redo-alt"></i>
                                    View Revision(s)
                                </button>
                            </router-link>
                            <router-link :to="{path:'/disputes/'+ orderId}">
                                <button type="button" class="btn btn-dark btn-sm">
                                    <i class="fas fa-redo-alt"></i>
                                    View Disputes
                                </button>
                            </router-link>
                            <router-link :to="{path:'/myorder'}" type="button"
                                         class="btn btn-primary btn-sm" v-if="$gate.isWriter()">
                                <i class="fas fa-hand-o-left"></i>
                                Back
                            </router-link>
                            <router-link :to="{path:'/order'}" type="button"
                                         class="btn btn-primary btn-sm" v-if="$gate.isEditor() || $gate.isAdmin()">
                                <i class="fas fa-hand-o-left"></i>
                                Back
                            </router-link>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <flip-countdown :deadline="details.deadline.toString()"></flip-countdown>
                                <hr>
                                <div class="row" v-if="$gate.isWriter()">
                                    <div class="col">
                                        <button type="button" class="btn btn-dark btn-sm" @click="extDeadline">Request
                                            for Extension
                                        </button>
                                        <br>
                                        <router-link :to="{path:'/extension/'+ orderId}" type="button"
                                                     class="btn btn-primary btn-sm mt-2" v-if="extensions">Extension
                                            Requests
                                        </router-link>
                                    </div>
                                    <div class="col" v-if="isExt==1">
                                        <div class="form-group">
                                            <label for="deadline">New Deadline</label>
                                            <datetime type="datetime" :auto="true" :min-datetime="now" zone="local"
                                                      value-zone="UTC+3" v-model="formx.deadline"></datetime>
                                            <button v-if="formx.deadline" type="button"
                                                    class="btn btn-primary btn-sm mt-2" @click="extendDeadline">Request
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" v-if="$gate.isAdmin()">
                                    <div class="col">
                                        <button type="button" class="btn btn-dark btn-sm" @click="changeDeadline">Edit
                                            deadline
                                        </button>
                                        <br>
                                        <router-link :to="{path:'/extension/'+ orderId}" type="button"
                                                     class="btn btn-primary btn-sm mt-2" v-if="extensions">Extension
                                            Requests
                                        </router-link>
                                    </div>
                                    <div class="col" v-if="editDeadline==1">
                                        <div class="form-group">
                                            <label for="deadline">New Deadline</label>
                                            <datetime type="datetime" :auto="true" :min-datetime="now" zone="local"
                                                      value-zone="UTC+3" v-model="formd.deadline"
                                                      id="deadline"></datetime>
                                            <button v-if="formd.deadline" type="button"
                                                    class="btn btn-primary btn-sm mt-2" @click="helloDeadline">Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="box">
                                    <div class="box-header">
                                        <h4 class="box-title">Writer's Details</h4>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body no-padding table-responsive p-0">
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <th>Title</th>
                                                <th style="width: 40px">Details</th>
                                            </tr>
                                            <tr v-if="$gate.isAdminOrisEditor()">
                                                <td><b>Name</b></td>
                                                <td>
                                                    <span>{{writer.name}}</span>
                                                    <span style="color: rebeccapurple;" v-if="!this.writer">No writer assigned</span>
                                                </td>
                                            </tr>
                                            <tr v-if="$gate.isAdmin()">
                                                <td><b>Phone</b></td>
                                                <td>
                                                    <span>{{writer.phone_number}}</span>
                                                    <span style="color: rebeccapurple;" v-if="!this.writer">No writer assigned</span>
                                                </td>
                                            </tr>
                                            <tr v-if="$gate.isAdmin()">
                                                <td><b>Email</b></td>
                                                <td>
                                                    <span>{{writer.email}}</span>
                                                    <span style="color: rebeccapurple;" v-if="!this.writer">No writer assigned</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Cost Per word</b></td>
                                                <td><span>Ksh. {{details.amount}}</span></td>
                                            </tr>
                                            <tr>
                                                <td><b>Total Cost</b></td>
                                                <td><span>Ksh. {{details.total_amount}}</span></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                                <hr>
                                <div class="box">
                                    <div class="box-header">
                                        <h4 class="box-title">Order Details</h4>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body no-padding table-responsive p-0">
                                        <table class="table table-striped">
                                            <tbody>
                                            <tr>
                                                <th>Title</th>
                                                <th style="width: 40px">Details</th>
                                            </tr>
                                            <tr>
                                                <td><b>Order Number</b></td>
                                                <td><span>{{details.order_number}}</span></td>
                                            </tr>
                                            <tr>
                                                <td><b>Title</b></td>
                                                <td><span>{{details.title}}</span></td>
                                            </tr>
                                            <tr>
                                                <td><b>Level</b></td>
                                                <td><span>{{details.academic_level}}</span></td>
                                            </tr>
                                            <tr>
                                                <td><b>Discipline</b></td>
                                                <td><span>{{details.discipline}}</span></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><b>No. of words</b></td>
                                                <td><span>{{details.pages}} words</span></td>
                                            </tr>
                                            <tr>
                                                <td><b>No. of Sources</b></td>
                                                <td><span>{{details.sources}}</span></td>
                                            </tr>
                                            <tr>
                                                <td><b>Deadline</b></td>
                                                <td><span style="color: red;">{{details.deadline|myDatetime}}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Spacing</b></td>
                                                <td><span>{{details.spacing}}</span></td>
                                            </tr>
                                            <tr>
                                                <td><b>Paper Format</b></td>
                                                <td><span>{{details.paper_format}}</span></td>
                                            </tr>
                                            <tr v-if="$gate.isAdmin() || $gate.isWriter()">
                                                <td><b>Posted On</b></td>
                                                <td><span>{{details.created_at|myDatetime}}</span></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                            </div>
                            <div class="col-md-6">
                                <div class="box">
                                    <div class="box" style="margin-bottom: 10px;">
                                        <div class="alert alert-dark" role="alert">
                                            Files
                                        </div>
                                        <div class="box-body">
                                            <button type="button" class="btn btn-success btn-sm" @click="newModal"
                                                    v-if="$gate.isAdmin()">
                                                Add more files
                                            </button>
                                        </div>
                                    </div>
                                    <div class="box-body" v-if="this.filesCount > 0">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12" v-for="file in files"
                                                 :key="file.id">
                                                <a @click.prevent="download(file.id, file.path)"
                                                   style="cursor: pointer;">
                                                    <div class="info-box">
                                                        <span class="info-box-icon" style="background-color: green;"><i
                                                                class="fas fa-download"
                                                                style="color: white;"></i></span>

                                                        <div class="info-box-content">
                                                            <span class="info-box-text">{{file.path.substring(18)}}</span>
                                                        </div>
                                                        <!-- /.info-box-content -->
                                                    </div>
                                                </a>
                                                <!-- /.info-box -->
                                            </div>
                                            <!--                                            <button type="button" class="btn btn-primary" @click="downloadAll">Download all files</button>-->
                                        </div>
                                    </div>
                                    <div class="alert alert-warning alert-dismissible" v-if="this.filesCount == 0">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <h5><i class="icon fa fa-ban"></i> Alert!</h5>
                                        No files attached!!
                                    </div>
                                </div>
                                <hr>
                                <div class="box">
                                    <div class="box-body">
                                        <div class="alert alert-dark" role="alert">
                                            Order Description
                                        </div>
                                        <p>
                                            <a class="btn btn-primary btn-sm" data-toggle="collapse"
                                               href="#collapseExample" role="button" aria-expanded="false"
                                               aria-controls="collapseExample">
                                                <i class="fas fa-eye"></i>
                                                View
                                            </a>
                                        </p>
                                        <div class="collapse" id="collapseExample">
                                            <div class="card card-body">
                                                <div v-html="details.description"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="box mt-5" v-if="$gate.isAdmin()">
                                        <div class="box-body">
                                            <div class="alert alert-dark" role="alert">
                                                Rate this work
                                            </div>
                                            <star-rating v-bind:increment="0.5" :read-only="myRate" :rating="rating"
                                                         @rating-selected="setRating"></star-rating>
                                            <div class="mt-2" v-if="$gate.isAdmin()">
                                                <button class="btn btn-success btn-sm" @click="setRatting"><i
                                                    class="fas fa-star"></i>Rate
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box" v-if="$gate.isWriter()">
                                    <div class="box-body">
                                        <button type="button" class="btn btn-success btn-sm" @click="isCompleted">Upload
                                            completed/draft file
                                        </button>
                                    </div>
                                </div>
                                <hr>
                                <div class="box">
                                    <div class="box-body">
                                        <div class="alert alert-dark" role="alert">
                                            Completed
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12" v-for="com in completed"
                                                 :key="com.id">
                                                    <div class="info-box">
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">{{com.path.substring(18)}}</span>
                                                            <small style="color: blue;">{{com.created_at |
                                                                myDatetime}}
                                                            </small>
                                                            <span class="badge badge-pill badge-primary">{{com.type}}</span><br>
                                                            <hr>
                                                            <button type="button" class="btn btn-primary btn-sm" @click.prevent="download(com.id, com.path)">
                                                                <i class="fas fa-download"></i>
                                                            </button>
                                                            <button v-if="$gate.isWriter()" type="button" class="btn btn-danger btn-sm" @click="deleteFile(com.id)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                        <!-- /.info-box-content -->
                                                    </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <!--                                            <button type="button" class="btn btn-primary" @click="downloadAll">Download all files</button>-->
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="box-body" style="padding-top: 10px;">
                                    <div class="box">
                                        <div class="box-body">
                                            <button v-if="$gate.isEditor()" type="button" class="btn btn-success btn-sm"
                                                    @click="isEdited">Upload edited task
                                            </button>
                                            <div class="alert alert-dark" role="alert">
                                                Edited Copy
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px">
                                        <div class="col-md-6 col-sm-6 col-xs-12" v-for="edi in edited" :key="edi.id">
                                                <div class="info-box">
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">{{edi.path.substring(18)}}</span>
                                                        <small style="color: blue;">{{edi.created_at | myDatetime}}
                                                        </small>
                                                        <hr>
                                                        <button type="button" class="btn btn-success btn-sm" @click.prevent="download(edi.id, edi.path)">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                        <button v-if="$gate.isEditor() || $gate.isAdmin()" type="button" class="btn btn-danger btn-sm" @click="deleteFile(com.id)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                            <!-- /.info-box -->
                                        </div>
                                        <!--                                            <button type="button" class="btn btn-primary" @click="downloadAll">Download all files</button>-->
                                    </div>
                                </div>
                                <hr>
                                <div class="box">
                                    <div class="box-body">
                                        <div class="alert alert-dark" role="alert">
                                            Revision
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12" v-for="rev in revision"
                                                 :key="rev.id">
                                                <a @click.prevent="download(rev.id, rev.path)" style="cursor: pointer;">
                                                    <div class="info-box">
                                                        <span class="info-box-icon"
                                                              style="background-color: #e3342f;"><i
                                                                class="fas fa-download"
                                                                style="color: white;"></i></span>

                                                        <div class="info-box-content">
                                                            <span class="info-box-text">{{rev.path.substring(18)}}</span>
                                                            <small style="color: blue;">{{rev.created_at |
                                                                myDatetime}}
                                                            </small>
                                                            <span class="badge badge-pill badge-primary">{{rev.type}}</span>
                                                        </div>
                                                        <!-- /.info-box-content -->
                                                    </div>
                                                </a>
                                                <!-- /.info-box -->
                                            </div>
                                            <!--                                            <button type="button" class="btn btn-primary" @click="downloadAll">Download all files</button>-->
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="box" v-if="$gate.isAdmin() || $gate.isEditor()">
                                    <div class="box-body">
                                        <div class="alert alert-dark" role="alert">
                                            Actions
                                        </div>
                                        <span v-if="$gate.isAdmin()">
                                        <button type="button" class="btn btn-success btn-sm" @click="verify()">Verify
                                        </button>
                                            </span>
                                        <button type="button" class="btn btn-warning btn-sm" @click="revisionModal">
                                            Revision
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm" @click="BonusModal"
                                                v-if="$gate.isAdmin()">Bonus
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" @click="disputedModal"
                                                v-if="$gate.isAdmin()">
                                            Disputed
                                        </button>
                                        <span v-if="$gate.isAdmin() || $gate.isEditor()">
                                        <button type="button" class="btn btn-primary btn-sm" @click="notUploaded()"
                                                v-if="details.status == 3">
                                            Not Uploaded
                                        </button>
                                            </span>
                                        <!--                                        <button type="button" class="btn btn-danger btn-sm">Reject</button>-->
                                    </div>
                                </div>
                                <hr>
                                <div class="box" v-if="$gate.isAdmin()">
                                    <div class="box-body">
                                        <div class="alert alert-dark" role="alert">
                                            Reassign
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm mb-2"
                                                @click="reassignTrue()">Click to Reassign
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm  mb-2" @click="repost()">
                                            RePost
                                        </button>
                                        <div class="row justify-content-center" v-if="isReassign == 1">
                                            <div class="col-sm-12">
                                                <form @submit.prevent="doReassign">
                                                    <div class="form-group">
                                                        <v-select id="writerSelect" v-model="writer_obj" label="email"
                                                                  :options="writers"
                                                                  placeholder="Select New Writer"
                                                                  v-on:change="writerId()"></v-select>
                                                    </div>

                                                    <button type="submit" v-if="writer_obj" class="btn btn-dark btn-sm">
                                                        Complete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>

                        </div>
                    </div>
                    <div class="row justify-content-center" v-if="$gate.isAdminOrisWriter()">
                        <div class="col-md-12">
                            <div class="card-body composer">
                                <textarea v-model="message" placeholder="Write your question here..."></textarea><br>
                                <div class="col-md-10">
                                    <button class="btn btn-success btn-md pull-left" @click="sendMessage"><i
                                            class="fas fa-paper-plane"></i>&nbsp;Send message
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row" v-if="$gate.isAdminOrisWriter()">
                        <div class="card-body conversation" @new="handleIncoming">
                            <h1>Messages</h1>
                            <div class=" badge badge-pill badge-primary">{{typing}}</div>
                            <div class="card-body feed" ref="feed">
                                <ul>
                                    <li v-for="message in messages"
                                        :class="`message${message.to == users ? ' sent' : ' received'}`"
                                        :key="message.id">
                                        <div class="text">
                                            <span class="messo">{{ message.text }}</span><br/>
                                            <small class="date">{{message.created_at | myDatetime}}</small>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="addnewLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--                        <h5 class="modal-title">Add File(s)</h5>-->
                            <h5 class="modal-title" v-if="isEdit">Upload Edited File</h5>
                            <h5 class="modal-title" v-if="isComplete">Upload Completed/Draft File/Revision</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="validate()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="files">Select File</label>
                                    <input type="file" multiple class="form-control-file" @change="fieldChange"
                                           id="files">
                                    <p style="color: red">{{this.e_files}}</p>
                                </div>
                                <div class="form-group" v-if="isComplete">
                                    <label>Select appropriately</label><br>
                                    <radio name="type" value="draft" v-model="formc.type">
                                        Draft
                                    </radio>
                                    <radio name="type" value="complete" v-model="formc.type">
                                        Completed
                                    </radio>
                                    <radio name="type" value="revision" v-model="formc.type">
                                        Revision
                                    </radio>
                                    <p style="color: red;">{{this.e_type}}</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success" :disabled="btn_addfiles">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    Upload
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="fineModal" tabindex="-1" role="dialog" aria-labelledby="addnewLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Execute Fine</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="fine()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="percentage">Percentage</label>
                                    <input v-model="form.percentage" type="number" class="form-control" id="percentage"
                                           placeholder="percentage"
                                           :class="{ 'is-invalid': form.errors.has('percentage') }">
                                    <has-error :form="form" field="percentage"></has-error>
                                </div>
                                <div class="form-group">
                                    <label for="description">Reason</label>
                                    <textarea v-model="form.description" class="form-control" id="description" rows="3"
                                              :class="{ 'is-invalid': form.errors.has('description') }"></textarea>
                                    <has-error :form="form" field="description"></has-error>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i>
                                    Complete
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="BonusModal" tabindex="-1" role="dialog" aria-labelledby="addnewLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Award Bonus</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="Bonus()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="BonusPercentage">Percentage</label>
                                    <input v-model="formb.percentage" type="number" class="form-control"
                                           id="BonusPercentage"
                                           placeholder="BonusPercentage"
                                           :class="{ 'is-invalid': form.errors.has('BonusPercentage') }">
                                    <has-error :form="form" field="percentage"></has-error>
                                </div>
                                <div class="form-group">
                                    <label for="Bonusdescription">Reason</label>
                                    <textarea v-model="formb.description" class="form-control" id="Bonusdescription"
                                              rows="3"
                                              :class="{ 'is-invalid': form.errors.has('Bonusdescription') }"></textarea>
                                    <has-error :form="form" field="Bonusdescription"></has-error>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i>
                                    Complete
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="vld-parent">
                <loading name="loader" :active.sync="isLoading"
                         :can-cancel="false"
                         :is-full-page="fullPage"></loading>
            </div>
            <div class="modal fade" id="disputedModal" tabindex="-1" role="dialog" aria-labelledby="addnewLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addnewLabel">Disputed Order</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="disputed()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Reason</label>
                                    <textarea v-model="formp.description" class="form-control"
                                              rows="3"
                                              :class="{ 'is-invalid': form.errors.has('Bonusdescription') }"></textarea>
                                    <has-error :form="form" field="Bonusdescription"></has-error>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i>
                                    Complete
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="revisionModal" tabindex="-1" role="dialog" aria-labelledby="addnewLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Submit Revision</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="makeRevision()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input v-model="formr.title" type="text" class="form-control" id="title"
                                           placeholder="Title"
                                           :class="{ 'is-invalid': formr.errors.has('title') }">
                                    <small style="color: red" v-if="error && errors.title">{{ errors.title[0] }}
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="description">Revision Instructions</label>
                                    <vue-editor v-model="formr.instruction"></vue-editor>
                                    <small style="color: red" v-if="error && errors.instruction">{{ errors.instruction[0] }}
                                    </small>
                                </div>
                                <div class="form-group justify-content-center">
                                    <label for="files">Attach file(s) if need be</label>
                                    <input type="file" multiple class="form-control-file" @change="fieldChange"
                                           id="revfiles">
                                    <small style="color: red" v-if="error && errors.files">{{ errors.files[0] }}
                                    </small>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i>
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import FlipCountdown from 'vue2-flip-countdown';
    import Countdown from 'vuejs-countdown';
    import 'vue-datetime/dist/vue-datetime.css';
    import 'vue-select/dist/vue-select.css';
    import {VueEditor} from "vue2-editor";
    // Import component
    import Loading from 'vue-loading-overlay';
    // Import stylesheet
    import 'vue-loading-overlay/dist/vue-loading.css';

    export default {
        components: {
            FlipCountdown,
            Countdown,
            VueEditor,
            Loading
        },
        props: {
            user: {
                type: Object,
                required: true
            },

        },
        data() {
            return {
                isLoading: false,
                fullPage: true,
                errors: {},
                error: false,
                isReassign: 0,
                writer_obj: '',
                isExt: 0,
                btn_addfiles: false,
                editDeadline: 0,
                newDeadline: '',
                now: moment().format('YYYY-MM-DD HH:mm:ss'),
                rating: 0,
                rated: '',
                myRate: false,
                message: '',
                deadline: '',
                typing: '',
                users: {},
                isComplete: false,
                isEdit: false,
                e_files: '',
                e_deadline: '',
                completed: {},
                revision: {},
                e_type: '',
                edited: {},
                messages: [],
                orderId: this.$route.params.orderId,
                writers: {},
                details: {},
                filesCount: '',
                files: {},
                extensions: {},
                writer: {},
                attachments: [],
                formf: new FormData(),
                forme: new FormData(),
                formrevFile: new FormData(),
                formr: new Form({
                    title: '',
                    instruction: '',
                    orderId: this.$route.params.orderId,
                }),
                form: new Form({
                    percentage: '',
                    description: '',
                    orderId: this.$route.params.orderId,
                }),
                formb: new Form({
                    percentage: '',
                    description: '',
                    orderId: this.$route.params.orderId,
                }),
                formd: new Form({
                    deadline: ''
                }),
                formp: new Form({
                    description: '',
                    orderId: this.$route.params.orderId,
                }),
                formx: new Form({
                    deadline: '',
                    orderId: this.$route.params.orderId,
                }),
                formA: new Form({
                    writer: '',
                    orderId: this.$route.params.orderId,
                }),
                formc: new Form({
                    type: ''
                })
            }
        },
        mounted() {
            console.log(this.deadline);

            Echo.private(`message.${this.user.id}`)
                .listen('ChatEvent', (e) => {
                    this.messages.push(e.message);
                })
                .listenForWhisper('typing', (e) => {
                    if (e.name != '') {
                        this.typing = 'typing..'
                    } else {
                        this.typing = ''
                    }
                });
        },
        methods: {
            deleteFile(fileId){
                if (this.$gate.isWriter()){
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Delete this File??",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.value) {
                            axios.delete("/api/delete_file/" + fileId).then(() => {
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
            notUploaded() {
                if (this.$gate.isAdmin() || this.$gate.isEditor()) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Mark it as not uploaded??",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, mark it!'
                    }).then((result) => {
                        if (result.value) {
                            axios.patch("/api/not_uploaded/" + this.orderId).then(() => {
                                Fire.$emit('entry');
                                Swal.fire(
                                    'Done!',
                                    'Marked as not completed!!',
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
            reassignTrue() {
                if (this.$gate.isAdmin()) {
                    this.isReassign = 1;
                }
            },
            doReassign() {
                if (this.$gate.isAdmin()) {
                    this.writerId();

                    this.formA.post("/api/reassignOrder").then(() => {
                        this.isReassign = 0;
                        this.formA.reset();
                        Fire.$emit('entry');
                        Swal.fire(
                            'Reassigned!',
                            'Reassigning successful!!',
                            'success'
                        )
                    }).catch(error => {
                        this.errors = error.response.data.errors;
                        Swal.fire({
                            type: 'error',
                            title: 'Error!!',
                            text: error.response.data.msg,
                        })
                    });
                }
            },
            writerId() {
                if (this.writer_obj) {
                    this.formA.writer = this.writer_obj['id'];
                } else if (!this.writer_obj) {
                    this.formA.writer = '';
                }
            },
            getWriters() {
                axios.get("/api/getwriters").then(({data}) => ([this.writers = data]));
            },
            getRequests() {
                axios.get("/api/extension/" + this.orderId).then(({data}) => ([this.extensions = data]));
            },
            extendDeadline() {
                if (this.$gate.isWriter()) {
                    this.formx.deadline = moment(this.formx.deadline).format('YYYY-MM-DD HH:mm:ss')
                    this.formx.post("/api/isExtension").then(() => {
                        this.isExt = 0;
                        Fire.$emit('entry');
                        Swal.fire(
                            'Sent!',
                            'Request sent!!',
                            'success'
                        )
                    }).catch(error => {
                        this.errors = error.response.data.errors;
                        Swal.fire({
                            type: 'error',
                            title: 'Error!!',
                            text: error.response.data.msg,
                        })
                    });
                }
            },
            extDeadline() {
                this.isExt = 1;
            },
            helloDeadline() {
                if (this.$gate.isAdmin()) {
                    this.formd.deadline = moment(this.formd.deadline).format('YYYY-MM-DD HH:mm:ss')
                    this.formd.put("/api/editDeadline/" + this.orderId).then(() => {
                        this.editDeadline = 0;
                        Fire.$emit('entry');
                        Swal.fire(
                            'Changed!',
                            'Deadline Changed!!',
                            'success'
                        )
                    }).catch(error => {
                        this.errors = error.response.data.errors;
                        Swal.fire({
                            type: 'error',
                            title: 'Error!!',
                            text: error.response.data.msg,
                        })
                    });
                }
            },
            changeDeadline() {
                this.editDeadline = 1;
            },
            revisionFiles(revisionId){
                for (let i = 0; i < this.attachments.length; i++) {
                    this.formrevFile.append('files[]', this.attachments[i]);
                }
                this.formrevFile.append('orderId', this.orderId);
                const config = {headers: {'Content-Type': 'multipart/form-data'}};

                axios.post('/api/revisionFiles/' + revisionId, this.formrevFile, config).then(response => {
                    this.loading = false;
                    Fire.$emit('entry');
                    Swal.fire(
                        'Success!',
                        'Revision Sent!!',
                        'success'
                    )
                    this.formr.reset();
                    $('#revisionModal').modal('hide');
                    this.isLoading = false;

                })
                    .catch(error => {
                        this.isLoading = false;
                        this.errors = error.response.data.errors;
                        Swal.fire({
                            type: 'error',
                            title: 'Error!!',
                            text: error.response.data.msg,

                        })
                    });
            },
            makeRevision() {
                this.isLoading = true;
                this.formr.post('/api/makerevision')
                    .then(({data}) => {
                        if (this.attachments.length > 0){
                            this.revisionFiles(data);
                        }else if (this.attachments.length == 0){
                            Fire.$emit('entry');
                            Swal.fire(
                                'Success!',
                                'Revision Sent!!',
                                'success'
                            );
                            this.formr.reset();
                            $('#revisionModal').modal('hide');
                            this.isLoading = false;
                        }
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
            fine() {
                this.form.post('/api/fine')
                    .then(() => {
                        Fire.$emit('entry');
                        Swal.fire(
                            'Fined!',
                            'Fine administered!!',
                            'success'
                        )
                        this.form.reset();
                        $('#fineModal').modal('hide');
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                        swal.fire({
                            type: 'error',
                            title: 'Error!!',
                            text: error.response.data.msg,
                        })
                    })
            },
            Bonus() {
                this.formb.post('/api/bonus')
                    .then(() => {
                        Fire.$emit('entry');
                        Swal.fire(
                            'Bonus!',
                            'Bonus Awarded!!',
                            'success'
                        )
                        this.formb.reset();
                        $('#BonusModal').modal('hide');
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                        swal.fire({
                            type: 'error',
                            title: 'Error!!',
                            text: error.response.data.msg,
                        })
                    })
            },
            disputed() {
                this.formp.post('/api/disputed')
                    .then(() => {
                        Fire.$emit('entry');
                        Swal.fire(
                            'Disputed!',
                            'Disputed!!',
                            'success'
                        )
                        this.formb.reset();
                        $('#disputedModal').modal('hide');
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                        swal.fire({
                            type: 'error',
                            title: 'Error!!',
                            text: error.response.data.msg,
                        })
                    })
            },
            verify() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Verify??",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, verify it!'
                }).then((result) => {
                    if (result.value) {
                        axios.post("/api/verify_task/" + this.orderId).then(() => {
                            Fire.$emit('entry');
                            Swal.fire(
                                'Placed!',
                                'Successfully verified as completed!!',
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
            },
             repost() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Repost a fresh??",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Repost it!'
                }).then((result) => {
                    if (result.value) {
                        axios.post("/api/repost/" + this.orderId).then(() => {
                            Fire.$emit('entry');
                            Swal.fire(
                                'Reposted!',
                                'Successfully reposted for new takes!!',
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
            },
            isEdited() {
                this.isEdit = true;
                this.newModal();
            },
            isCompleted() {
                this.formc.reset();
                this.isComplete = true;
                this.newModal();
            },
            setRatting() {
                if (this.rated != 0) {
                    Swal.fire({
                        type: 'error',
                        title: 'already rated!',
                        text: 'you have already rated this work',
                    });
                    return;
                }
                axios.post('/api/rating', {
                    OrderId: this.orderId,
                    UserId: this.writer.id,
                    Rating: this.rating,
                }).then(response => {
                    Swal.fire({
                        type: 'success',
                        title: 'Rating Submited!!',
                        text: 'Order rated successfully',
                    })
                }).catch(response => {
                    //error
                });
            },
            getRate() {
                axios.get("/api/myRate/" + this.orderId).then(({data}) => ([this.rating = data]));
            },
            getWriter() {
                axios.get("/api/writer/" + this.orderId).then(({data}) => ([this.writer = data]));
            },
            getMyDeadline() {
                axios.get("/api/deadline/" + this.orderId).then(({data}) => ([this.deadline = data.deadline]));
            },
            getRating() {
                axios.get("/api/rate/" + this.orderId).then(({data}) => ([this.rated = data]));
            },
            validate() {
                if (this.attachments.length == 0) {
                    this.e_files = 'This field is required';
                    return false;
                } else if (this.isComplete && !this.formc.type) {
                    this.e_type = "This field is required";
                } else {
                    this.submit();
                }
            },
            submit() {
                this.btn_addfiles = true;
                for (let i = 0; i < this.attachments.length; i++) {
                    this.formf.append('files[]', this.attachments[i]);
                }
                this.formf.append('type', this.formc.type);

                const config = {headers: {'Content-Type': 'multipart/form-data'}};
                if (this.isComplete) {
                    axios.post('/api/uploadcomplete/' + this.orderId, this.formf, config).then(response => {
                        Fire.$emit('entry');
                        $('#addnew').modal('hide');
                        this.form.reset();
                        Swal.fire({
                            type: 'success',
                            title: 'Submited!!',
                            text: 'File(s) sent successfully',
                        });
                        this.btn_addfiles = false;
                    })
                        .catch(response => {
                            this.btn_addfiles = false;
                        });
                } else if (this.isEdit) {
                    axios.post('/api/uploadedited/' + this.orderId, this.formf, config).then(response => {
                        Fire.$emit('entry');
                        $('#addnew').modal('hide');
                        this.form.reset();
                        Swal.fire({
                            type: 'success',
                            title: 'Submited!!',
                            text: 'File(s) uploaded successfully',
                        });
                        this.btn_addfiles = false;
                    })
                        .catch(response => {
                            this.btn_addfiles = false;
                        });
                } else {
                    axios.post('/api/addfiles/' + this.orderId, this.formf, config).then(response => {
                        Fire.$emit('entry');
                        $('#addnew').modal('hide');
                        this.form.reset();
                        Swal.fire({
                            type: 'success',
                            title: 'Submited!!',
                            text: 'Files added successfully',
                        })
                    })
                        .catch(response => {
                            //error
                        });
                }
            },
            fieldChange(e) {
                let selectedFiles = e.target.files;
                if (!selectedFiles.length) {
                    return false;
                }
                for (let i = 0; i < selectedFiles.length; i++) {
                    this.attachments.push(selectedFiles[i]);
                }
                console.log(this.attachments);
            },
            newModal() {
                $("#files").val('');
                this.form.reset();
                this.attachments = [];
                $('#addnew').modal('show');
            },
            revisionModal() {
                this.formr.reset();
                this.form.clear();
                $('#revisionModal').modal('show');
            },
            fineModal() {
                this.form.reset();
                $('#fineModal').modal('show');
            },
            BonusModal() {
                this.form.reset();
                $('#BonusModal').modal('show');
            },
            disputedModal() {
                this.form.reset();
                $('#disputedModal').modal('show');
            },
            handleIncoming(message) {
                this.messages.push(message);
            },
            scrollToBottom() {
                setTimeout(() => {
                    this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight;
                }, 50);
            },
            sendMessage() {
                console.log(this.orderId);
                if (this.message == '') {
                    return;
                }
                axios.post('/api/messenger/send', {
                    text: this.message,
                    OrderId: this.orderId,
                    contact_id: this.users,
                }).then((response) => {
                    this.messages.push(response.data);
                    this.message = '';
                })
            },
            download(id, path) {
                axios.get("/api/download/" + id, {responseType: 'blob'})
                    .then((response) => {
                        var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                        var fileLink = document.createElement('a');
                        console.log(fileLink);
                        fileLink.href = fileURL;
                        fileLink.setAttribute('download', path.substring(18));
                        document.body.appendChild(fileLink);
                        fileLink.click();
                    });
            },
            getDetails() {
                axios.get("/api/order/" + this.orderId).then(({data}) => ([this.details = data]));
            },
            getFilesCount() {
                axios.get("/api/filescount/" + this.orderId).then(({data}) => ([this.filesCount = data]));
            },
            getFiles() {
                axios.get("/api/getfiles/" + this.orderId).then(({data}) => ([this.files = data]));
            },
            getUser() {
                if (this.$gate.isAdmin()) {
                    axios.get("/api/getUser/" + this.orderId).then(({data}) => ([this.users = data]));
                }
                if (this.$gate.isWriter()) {
                    axios.get("/api/getAdmin/").then(({data}) => ([this.users = data]));
                }
            },
            getEditedFiles() {
                axios.get("/api/getedited/" + this.orderId).then(({data}) => ([this.edited = data]));
            },
            getCompletedFiles() {
                axios.get("/api/getcompleted/" + this.orderId).then(({data}) => ([this.completed = data]));
            },
            getRevisionFiles() {
                axios.get("/api/getrevision/" + this.orderId).then(({data}) => ([this.revision = data]));
            },
            getMessages() {
                axios.get("/api/getMessage/" + this.orderId).then((response) => (this.messages = response.data));
            },
            setRating: function (rating) {
                this.rating = rating;
            },
            hasRated() {
                if (this.rated != 0) {
                    this.myRate = true;
                    console.log('ftyui');
                } else {
                    console.log('ftyui');
                }
            },
        },
        watch: {
            messages(messages) {
                this.scrollToBottom();
            },
            message() {
                Echo.private(`message.${this.user.id}`)
                    .whisper('typing', {
                        name: this.message
                    });
            },
        },
        created() {
            this.getDetails();
            this.getFilesCount();
            this.getEditedFiles();
            this.getMessages();
            this.getFiles();
            this.getWriter();
            this.getRating();
            this.getRate();
            this.hasRated();
            this.getUser();
            this.getMyDeadline();
            this.getCompletedFiles();
            this.getRevisionFiles();
            this.getWriters();
            Fire.$on('entry', () => {
                this.getFiles();
                this.getFilesCount();
                this.hasRated();
                this.getDetails();
                this.getEditedFiles();
                this.getCompletedFiles();
                this.getMyDeadline();
                this.getRequests();
                this.getWriters();
            })
        }
    }
</script>
<style lang="scss" scoped>
    .composer textarea {
        width: 80%;
        margin: 10px;
        border-radius: 3px;
        border: 1px solid lightgray;
        padding: 6px;
    }

    .conversation {
        overflow-y: scroll;
        flex: 5;
        display: flex;
        flex-direction: column;
        justify-content: space-between;

        h1 {
            font-size: 20px;
            padding: 10px;
            margin: 0;
            border-bottom: 1px dashed lightgray;
        }
    }

    .messo {
        font-size: 15px;
        font-weight: 700;
    }

    .date {
        color: #9e9e9e;
        font-weight: 700;
    }

    span.unread {
        background: #82e0a8;
        color: #fff;
        position: absolute;
        right: 11px;
        top: 20px;
        display: flex;
        font-weight: 700;
        min-width: 20px;
        justify-content: center;
        align-items: center;
        line-height: 20px;
        font-size: 12px;
        padding: 0 4px;
        border-radius: 50%;
    }

    .feed {
        background: #f0f0f0;
        height: 100%;
        max-height: 470px;
        overflow: scroll;

        ul {
            list-style-type: none;
            padding: 5px;

            li {
                &.message {
                    margin: 10px 0;
                    width: 100%;

                    .text {
                        max-width: 400px;
                        border-radius: 5px;
                        padding: 12px;
                        display: inline-block;
                    }

                    &.received {
                        text-align: right;

                        .text {
                            background: #00e676;
                        }
                    }

                    &.sent {
                        text-align: left;

                        .text {
                            background: #81c4f9;
                        }
                    }
                }
            }
        }
    }
</style>
