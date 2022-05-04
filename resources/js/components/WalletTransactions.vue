<template>
    <div class="container-fluid pl-0 pr-0 ml-0 mr-0">
        <div class="row justify-content-center pl-0 pr-0 ml-0 mr-0">
            <div class="col-md-12 mt-4 pl-0 pr-0 ml-0 mr-0">
                <div class="card pl-0 pr-0 ml-0 mr-0">
                    <div class="card-header">
                        <h3 class="card-title">Transactions</h3>

                        <div class="card-tools"></div>
                    </div>

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
                                <tr
                                    v-for="trans in transactions.data"
                                    :key="trans.id"
                                >
                                    <td>{{ trans.id }}</td>
                                    <td>{{ trans.created_at | myDatetime }}</td>
                                    <td>
                                        <router-link
                                            :to="{
                                                path:
                                                    '/orderdetails/' +
                                                    trans.order_id
                                            }"
                                            >#{{ trans.order_number }}
                                        </router-link>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-pill badge-warning"
                                            v-if="trans.type == 1"
                                            >Fine</span
                                        >
                                        <span
                                            class="badge badge-pill badge-info"
                                            v-if="trans.type == 3"
                                            >PayOut</span
                                        >
                                        <span
                                            class="badge badge-pill badge-warning"
                                            v-if="trans.type == 4"
                                            >Bonus</span
                                        >
                                        <span
                                            class="badge badge-pill badge-success"
                                            v-if="trans.type == 0"
                                            >Awarded</span
                                        >
                                        <span
                                            class="badge badge-pill badge-dark"
                                            v-if="trans.type == 5"
                                            >Fine Cancelled</span
                                        >
                                        <span
                                            class="badge badge-pill badge-danger"
                                            v-if="trans.type == 6"
                                            >Cancelled</span
                                        >
                                        <span
                                            class="badge badge-pill badge-secondary"
                                            v-if="trans.type == 8"
                                            >Disputed</span
                                        >
                                        <span
                                            class="badge badge-pill badge-secondary"
                                            v-if="trans.type == 9"
                                            >Resolved</span
                                        >
                                    </td>
                                    <td>
                                        <span v-if="trans.type == 1"
                                            >{{
                                                Math.trunc(trans.percentage)
                                            }}%</span
                                        >
                                    </td>
                                    <td>Ksh. {{ trans.amount }}</td>
                                    <td>Ksh. {{ trans.balance }}</td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-warning btn-sm"
                                            v-if="trans.type == 1"
                                            @click="
                                                reasonModal(trans.description)
                                            "
                                        >
                                            Reason
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-dark btn-sm"
                                            v-if="trans.type == 5"
                                            @click="
                                                reasonModal(trans.description)
                                            "
                                        >
                                            Details
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <pagination
                            :data="transactions"
                            @pagination-change-page="getResults"
                        ></pagination>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="modal fade"
            id="reasonModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="addnewLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addnewLabel">Reason</h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>{{ reason }}</p>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-danger"
                            data-dismiss="modal"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "WalletTransactions",
    data() {
        return {
            transactions: {},
            reason: ""
        };
    },
    methods: {
        getResults(page = 1) {
            axios.get("api/transactions?page=" + page).then(response => {
                this.transactions = response.data;
            });
        },
        reasonModal(reason) {
            this.reason = reason;
            $("#reasonModal").modal("show");
        },
        getTransactions() {
            
                axios
                    .get("api/transactions")
                    .then(({ data }) => [(this.transactions = data)]);
            
        }
    },
    created() {
        this.getTransactions();
    }
};
</script>

<style scoped></style>
