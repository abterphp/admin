<% extends("layouts/backend/login") %>

<% part("content") %>

<% include("./partials/backend/general/messages", compact("errorMessages", "successMessages")) %>

<div class="logincard">
    <div class="pmd-card card-default pmd-z-depth">
        <div class="login-card">
            <p class="only-js-login-warning"><i class="material-icons">warning</i>&nbsp;{{ tr('admin:jsOnly') }}</p>

            <form method="post" action="{{! route('login-post') !}}">
                {{! csrfInput() !}}

                <input type="hidden" id="login-password" name="password">

                <div class="pmd-card-title card-header-border text-center">
                    <h3>{{ tr("admin:signIn") }}</h3>
                </div>

                <div class="pmd-card-body">
                    <div class="alert alert-warning" role="alert">Add text...</div>
                    <div class="form-group pmd-textfield pmd-textfield-floating-label">
                        <label for="login-username" class="control-label pmd-input-group-label">{{ tr("admin:username") }}</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="material-icons md-dark pmd-sm">perm_identity</i>
                            </div>
                            <input type="text" class="form-control" name="username" id="login-username">
                            <span class="pmd-textfield-focused"></span>
                        </div>
                    </div>

                    <div class="form-group pmd-textfield pmd-textfield-floating-label">
                        <label for="login-raw-password" class="control-label pmd-input-group-label">{{ tr("admin:password") }}</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="material-icons md-dark pmd-sm">lock_outline</i>
                            </div>
                            <input type="password" class="form-control" id="login-raw-password">
                            <span class="pmd-textfield-focused"></span>
                        </div>
                    </div>
                </div>
                <div class="pmd-card-footer card-footer-no-border card-footer-p16 text-center">
                    <button id="login-submit" type="submit" class="btn pmd-ripple-effect btn-primary btn-block" disabled>{{ tr("admin:login") }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{! assetJs('admin-login') !}}

<% endpart %>