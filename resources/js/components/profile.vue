<style>
    .widget-user-header{
        background-position: center center;
        background-size: cover;
        height: 250px !important;
    }
    .widget-user .card-footer{
        padding: 0;
    }
</style>
<template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-3">
                <div class="card card-widget widget-user">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header bg-black" style="background: url('./img/background.jpg') center center;">
                        <h3 class="widget-user-username">{{this.form.name}}</h3>
                        <h5 class="widget-user-desc">{{this.form.type}}</h5>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" :src="getProfilePhoto()"  alt="User Avatar">
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">{{this.form.name}}</h5>
                                    <span class="description-text">Name</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">{{this.form.phone_number}}</h5>
                                    <span class="description-text">Contact</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4">
                                <div class="description-block">
                                    <h5 class="description-header" v-if="this.form.level_id == 2">Junior</h5>
                                    <h5 class="description-header" v-if="this.form.level_id == 1">Starter</h5>
                                    <h5 class="description-header" v-if="this.form.level_id == 3">Senior</h5>
                                    <span class="description-text">Level</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="active"><a  class="nav-link active show" href="#settings" data-toggle="tab" >Settings</a></li>
                            </ul>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Activity Tab -->

                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="timeline">

                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane active" id="settings">
                                    <form class="form-horizontal">
                                        <div class="form-group">
                                            <label for="inputName"  class="col-sm-2 control-label">Name</label>

                                            <div class="col-sm-10">
                                                <input type="text" v-model="form.name" class="form-control" id="inputName" placeholder="Name" :class="{ 'is-invalid': form.errors.has('name') }">
                                                <has-error :form="form" field="name"></has-error>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                                            <div class="col-sm-10">
                                                <input type="email" v-model="form.email" class="form-control" id="inputEmail" placeholder="Email"  :class="{ 'is-invalid': form.errors.has('email') }">
                                                <has-error :form="form" field="email"></has-error>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputPhone" class="col-sm-4 control-label">Phone</label>

                                            <div class="col-sm-10">
                                                <input type="tel" v-model="form.phone_number" class="form-control" id="inputPhone" placeholder="Phone"  :class="{ 'is-invalid': form.errors.has('phone') }">
                                                <has-error :form="form" field="phone"></has-error>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputPhone" class="col-sm-2 control-label">Bank Name</label>

                                            <div class="col-sm-10">
                                                <input type="text" v-model="form.bank" class="form-control" id="inputPhone" placeholder="Bank"  :class="{ 'is-invalid': form.errors.has('bank') }">
                                                <has-error :form="form" field="bank"></has-error>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputPhone" class="col-sm-4 control-label">Bank Account Number</label>

                                            <div class="col-sm-10">
                                                <input type="number" v-model="form.bank_account" class="form-control" id="inputPhone" placeholder="Account Number"  :class="{ 'is-invalid': form.errors.has('bank_account') }">
                                                <has-error :form="form" field="bank_account"></has-error>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="typo__label">Selcet areas of your specialities</label>

                                            <div class="col-sm-10">
                                                <span class="multiselect">
                                                    <multiselect class="multiselect" v-model="form.value" :searchable="false" placeholder="Choose areas of your specialities" :max-height="600"  :options="options" :multiple="true" :close-on-select="false" :taggable="true"  @tag="addTag" ></multiselect>
                                                    </span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="photo" class="col-sm-2 control-label">Profile Photo</label>
                                            <div class="col-sm-12">
                                                <input type="file" @change="updateProfile"   name="photo" class="form-input">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputSkills" class="col-sm-6 control-label">Password (leave empty if not changing)</label>

                                            <div class="col-sm-10">
                                                <input type="password" v-model="form.password"class="form-control" id="inputSkills" placeholder="password"  :class="{ 'is-invalid': form.errors.has('password') }">
                                                <has-error :form="form" field="password"></has-error>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <button type="submit" @click.prevent="updateInfo" class="btn btn-danger">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div>
                        <!-- /.card-body -->
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect';
    export default {
        components: { Multiselect },
        data(){
            return{
                options: ['Accounting',
'Advertising','Aeronautical Engineering','Aeronautics','Aerospace Science','African-American Studies',
    'Agricultural Studies','American History','American-Literature', 'Anthropology','Antique Literature',
    'Application Essay','Architecture','ArtAsian Literature','Asian Studies','Astronomy','Aviation','Biology',
    'Business','Canadian Studies','Case Study','Chemistry', 'Civil & Structural Engineering','Communications and Media'
    ,'Communications Strategies','Company analysis','Computer Science','Creative Writing','CriminologyDance', 'Design analysis',
    'DramaEast European Studies','E-Commerce','Economics','Education','Education Theories','Engineering','English','Environmental Issues',
    'EthicsFilm', 'Finance','Geography','Healthcare','History','Holocaust','Information Technology','Internet','Investment','IT Management',
    'JournalismLatin-American Studies','LawLegal Issues','Linguistics','Literature','Logistics','Management','Marketing','Mathematics','Mechanical Engineering',
    'Medicine and Health Studies','Movies','Music','Native-American Studies','Nature','Nursing','Nutrition','Paintings','Pedagogy','Pharmacology','Philosophy','Physics',
    'Political Science','Psychology','Public Relations','Religion and Theology','Religious Studies','Shakespeare Literature'
,'Sociology','Sports','Statistics','Teacher’s', 'Career','Technology','Theat','Tourism','Trade','Web Design','West-European Studies'
        ],
                form: new Form({
                    id:'',
                    name:'',
                    email: '',
                    phone_number: '',
                    password:'',
                    photo:'',
                    level_id:'',
                    bank:'',
                    bank_account:'',
                    value:[],
                })
            }
        },
        methods:{
            getValues(){
                axios.get("api/values").then(({data}) => ([this.form.value = data['value']]));
            },
            getProfilePhoto(){
                let photo = (this.form.photo.length > 200) ? this.form.photo : "img/profile/"+ this.form.photo ;
                return photo;
            },
            addTag (newTag) {
                const tag = {
                    name: newTag,
                    code: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
                }
                this.options.push(tag)
                this.value.push(tag)
            },
            updateInfo(){
                this.$Progress.start();
                this.form.put('api/profile/')
                    .then(()=>{
                        Fire.$emit('AfterCreate');
                        Swal.fire({
                            type: 'success',
                            title: 'User Information Updated',
                        });
                        this.$Progress.finish();
                    })
                    .catch(()=>{
                        Swal.fire({
                            type: 'error',
                            title: 'Error!!',
                            text: 'error in uploading changes',
                        })
                        this.$Progress.fail();
                    });
            },
            updateProfile(e){
                let file = e.target.files[0];
                let reader = new FileReader();
                let limit = 1024 * 1024 * 2;
                if(file['size'] > limit){

                    swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'You are uploading a large file',
                    })
                    return false;
                }

                reader.onloadend = (file)=>{
                    //console.log('RESULT',reader.result);
                    this.form.photo = reader.result;
                }
                reader.readAsDataURL(file);

            }
        },
        mounted() {

            console.log('Component mounted.')
        },
        created() {
            axios.get("api/profile")
                .then(({ data }) => (this.form.fill(data)));
            this.getValues();
        }

    }
</script>
<style>
    .multiselect {
        box-sizing: content-box;
        display: block;
        position: relative;
        width: 100%;
        min-height: 40px;
        text-align: left;
        color: #35495e;
    }
    .multiselect * {
        box-sizing: border-box;
    }
    .multiselect:focus {
        outline: none;
    }
    .multiselect--disabled {
        background: #ededed;
        pointer-events: none;
        opacity: 0.6;
    }
    .multiselect--active {
        z-index: 50;
    }
    .multiselect--active:not(.multiselect--above) .multiselect__current,
    .multiselect--active:not(.multiselect--above) .multiselect__input,
    .multiselect--active:not(.multiselect--above) .multiselect__tags {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
    .multiselect--active .multiselect__select {
        transform: rotateZ(180deg);
    }
    .multiselect--above.multiselect--active .multiselect__current,
    .multiselect--above.multiselect--active .multiselect__input,
    .multiselect--above.multiselect--active .multiselect__tags {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    .multiselect__input,
    .multiselect__single {
        position: relative;
        display: inline-block;
        min-height: 20px;
        line-height: 20px;
        border: none;
        border-radius: 5px;
        background: #fff;
        padding: 0 0 0 5px;
        width: calc(100%);
        transition: border 0.1s ease;
        box-sizing: border-box;
        margin-bottom: 8px;
        vertical-align: top;
    }
    .multiselect__input::placeholder {
        color: #35495e;
    }
    .multiselect__tag ~ .multiselect__input,
    .multiselect__tag ~ .multiselect__single {
        width: auto;
    }
    .multiselect__input:hover,
    .multiselect__single:hover {
        border-color: #cfcfcf;
    }
    .multiselect__input:focus,
    .multiselect__single:focus {
        border-color: #a8a8a8;
        outline: none;
    }
    .multiselect__single {
        padding-left: 5px;
        margin-bottom: 8px;
    }
    .multiselect__tags-wrap {
        display: inline;
    }
    .multiselect__tags {
        min-height: 40px;
        display: block;
        padding: 8px 40px 0 8px;
        border-radius: 5px;
        border: 1px solid #e8e8e8;
        background: #fff;
        font-size: 14px;
    }
    .multiselect__tag {
        position: relative;
        display: inline-block;
        padding: 4px 26px 4px 10px;
        border-radius: 5px;
        margin-right: 10px;
        color: #fff;
        line-height: 1;
        background: #41b883;
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        max-width: 100%;
        text-overflow: ellipsis;
    }
    .multiselect__tag-icon {
        cursor: pointer;
        margin-left: 7px;
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        font-weight: 700;
        font-style: initial;
        width: 22px;
        text-align: center;
        line-height: 22px;
        transition: all 0.2s ease;
        border-radius: 5px;
    }
    .multiselect__tag-icon:after {
        content: "×";
        color: #266d4d;
        font-size: 14px;
    }
    .multiselect__tag-icon:focus,
    .multiselect__tag-icon:hover {
        background: #369a6e;
    }
    .multiselect__tag-icon:focus:after,
    .multiselect__tag-icon:hover:after {
        color: white;
    }
    .multiselect__current {
        line-height: 16px;
        min-height: 40px;
        box-sizing: border-box;
        display: block;
        overflow: hidden;
        padding: 8px 12px 0;
        padding-right: 30px;
        white-space: nowrap;
        margin: 0;
        text-decoration: none;
        border-radius: 5px;
        border: 1px solid #e8e8e8;
        cursor: pointer;
    }
    .multiselect__select {
        line-height: 16px;
        display: block;
        position: absolute;
        box-sizing: border-box;
        width: 40px;
        height: 38px;
        right: 1px;
        top: 1px;
        padding: 4px 8px;
        margin: 0;
        text-decoration: none;
        text-align: center;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    .multiselect__select:before {
        position: relative;
        right: 0;
        top: 65%;
        color: #999;
        margin-top: 4px;
        border-style: solid;
        border-width: 5px 5px 0 5px;
        border-color: #999999 transparent transparent transparent;
        content: "";
    }
    .multiselect__placeholder {
        color: #adadad;
        display: inline-block;
        margin-bottom: 10px;
        padding-top: 2px;
    }
    .multiselect--active .multiselect__placeholder {
        display: none;
    }
    .multiselect__content-wrapper {
        position: absolute;
        display: block;
        background: #fff;
        width: 100%;
        max-height: 240px;
        overflow: auto;
        border: 1px solid #e8e8e8;
        border-top: none;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
        z-index: 50;
        -webkit-overflow-scrolling: touch;
    }
    .multiselect__content {
        list-style: none;
        display: inline-block;
        padding: 0;
        margin: 0;
        min-width: 100%;
        vertical-align: top;
    }
    .multiselect--above .multiselect__content-wrapper {
        bottom: 100%;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        border-bottom: none;
        border-top: 1px solid #e8e8e8;
    }
    .multiselect__content::webkit-scrollbar {
        display: none;
    }
    .multiselect__element {
        display: block;
    }
    .multiselect__option {
        display: block;
        padding: 12px;
        min-height: 40px;
        line-height: 16px;
        text-decoration: none;
        text-transform: none;
        vertical-align: middle;
        position: relative;
        cursor: pointer;
        white-space: nowrap;
    }
    .multiselect__option:after {
        top: 0;
        right: 0;
        position: absolute;
        line-height: 40px;
        padding-right: 12px;
        padding-left: 20px;
        font-size: 13px;
    }
    .multiselect__option--highlight {
        background: #41b883;
        outline: none;
        color: white;
    }
    .multiselect__option--highlight:after {
        content: attr(data-select);
        background: #41b883;
        color: white;
    }
    .multiselect__option--selected {
        background: #f3f3f3;
        color: #35495e;
        font-weight: bold;
    }
    .multiselect__option--selected:after {
        content: attr(data-selected);
        color: silver;
    }
    .multiselect__option--selected.multiselect__option--highlight {
        background: #ff6a6a;
        color: #fff;
    }
    .multiselect__option--selected.multiselect__option--highlight:after {
        background: #ff6a6a;
        content: attr(data-deselect);
        color: #fff;
    }
    .multiselect--disabled .multiselect__current,
    .multiselect--disabled .multiselect__select {
        background: #ededed;
        color: #a6a6a6;
    }
    .multiselect__option--disabled {
        background: #ededed !important;
        color: #a6a6a6 !important;
        cursor: text;
        pointer-events: none;
    }
    .multiselect__option--group {
        background: #ededed;
        color: #35495e;
    }
    .multiselect__option--group.multiselect__option--highlight {
        background: #35495e;
        color: #fff;
    }
    .multiselect__option--group.multiselect__option--highlight:after {
        background: #35495e;
    }
    .multiselect__option--disabled.multiselect__option--highlight {
        background: #dedede;
    }
    .multiselect__option--group-selected.multiselect__option--highlight {
        background: #ff6a6a;
        color: #fff;
    }
    .multiselect__option--group-selected.multiselect__option--highlight:after {
        background: #ff6a6a;
        content: attr(data-deselect);
        color: #fff;
    }
    .multiselect-enter-active,
    .multiselect-leave-active {
        transition: all 0.15s ease;
    }
    .multiselect-enter,
    .multiselect-leave-active {
        opacity: 0;
    }
    .multiselect__strong {
        margin-bottom: 8px;
        line-height: 20px;
        display: inline-block;
        vertical-align: top;
    }

</style>
