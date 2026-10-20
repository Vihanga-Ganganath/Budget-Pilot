<?php require APPROOT . '/Views/shared/header.php'; ?>
<?php
$u = $data['user'];

// Photo link: new photos are only a file name (in assets/img/avatars), old ones a path inside public/
function avatarUrl($file) {
    if (empty($file)) return '';
    if (strpos($file, '/') !== false) return URLROOT . '/public/' . $file;
    return URLROOT . '/public/assets/img/avatars/' . $file;
}
$photo    = avatarUrl($u['profile_picture_url']);
$initials = strtoupper(substr($u['name'], 0, 1));
$genders  = ['male' => 'Male', 'female' => 'Female', 'other' => 'Other', 'prefer_not_to_say' => 'Prefer not to say'];
$currencies = ['USD' => 'USD - US Dollar ($)', 'EUR' => 'EUR - Euro (&euro;)', 'GBP' => 'GBP - British Pound (&pound;)',
               'LKR' => 'LKR - Sri Lankan Rupee (Rs)', 'INR' => 'INR - Indian Rupee (&#8377;)', 'AUD' => 'AUD - Australian Dollar ($)'];
?>

<div>
    <div class="dashboard-header" style="margin-bottom:20px;">
        <div>
            <h1 class="page__title">Profile &amp; Settings</h1>
            <p class="page__sub">Manage your personal information and application preferences.</p>
        </div>
    </div>

    <?php if (!empty($data['flash'])): ?>
        <p style="background:#DCFCE7; border:1px solid #BDE6CE; color:#166534; padding:12px 16px; border-radius:8px; margin:0 0 20px;"><?php echo htmlspecialchars($data['flash']); ?></p>
    <?php endif; ?>

    <div class="layout layout--even">
        <!-- ---------- Left column: profile ---------- -->
        <div class="col">

            <!-- ===== Profile form: identity + account details + preferences ===== -->
            <form action="<?php echo URLROOT; ?>/customer/settings" method="post" enctype="multipart/form-data" class="col">
                <input type="hidden" name="form" value="profile">

                <!-- Identity -->
                <section class="card identity">
                    <span class="identity__avatar" style="<?php echo $photo ? "background-image:url('" . htmlspecialchars($photo) . "')" : 'background-color:var(--color-primary)'; ?>"><?php echo $photo ? '' : htmlspecialchars($initials); ?></span>
                    <div class="identity__text">
                        <h2 class="identity__name"><?php echo htmlspecialchars($u['name']); ?></h2>
                        <p class="identity__role"><?php echo $u['household_role'] == 'head' ? 'Account Holder' : 'Family Member'; ?></p>
                        <label class="label" for="photo" style="margin-top:12px;">Change photo</label>
                        <input id="photo" name="photo" type="file" accept="image/*" />
                    </div>
                </section>

                <!-- Account details -->
                <section class="card">
                    <h2 class="card__title">Account Details</h2>
                    <p class="card__sub">The details you entered when you created your account. Change anything and press Save.</p>
                    <div class="fields">
                        <div class="field">
                            <label class="label" for="fName">Full name</label>
                            <input class="control" id="fName" name="name" type="text" value="<?php echo htmlspecialchars($u['name']); ?>" />
                        </div>
                        <div class="field">
                            <label class="label" for="fEmail">Email address</label>
                            <input class="control" id="fEmail" name="email" type="email" value="<?php echo htmlspecialchars($u['email']); ?>" />
                        </div>
                        <div class="field">
                            <label class="label" for="fIncome">Monthly income</label>
                            <div class="control control--money">
                                <span class="control__prefix">$</span>
                                <input id="fIncome" name="income" type="number" min="0" step="0.01" value="<?php echo htmlspecialchars($u['monthly_income'] ?? ''); ?>" />
                            </div>
                        </div>
                        <div class="field">
                            <label class="label" for="fSavings">Savings goal</label>
                            <div class="control control--money">
                                <span class="control__prefix">$</span>
                                <input id="fSavings" name="savings" type="number" min="0" step="0.01" value="<?php echo htmlspecialchars($u['financial_goals'] ?? ''); ?>" />
                            </div>
                        </div>
                        <div class="field">
                            <label class="label" for="fNic">National ID card (NIC)</label>
                            <input class="control" id="fNic" name="nic" type="text" value="<?php echo htmlspecialchars($u['nic'] ?? ''); ?>" />
                        </div>
                        <div class="field field--split">
                            <div>
                                <label class="label" for="fGender">Gender</label>
                                <div class="control control--select">
                                    <select id="fGender" name="gender">
                                        <option value="">Not set</option>
                                        <?php foreach ($genders as $value => $text): ?>
                                            <option value="<?php echo $value; ?>" <?php echo ($u['gender'] ?? '') == $value ? 'selected' : ''; ?>><?php echo $text; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="label" for="fAge">Age</label>
                                <input class="control" id="fAge" name="age" type="number" min="13" max="120" value="<?php echo htmlspecialchars($u['age'] ?? ''); ?>" />
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Financial preferences -->
                <section class="card">
                    <h2 class="card__title">Financial Preferences</h2>
                    <p class="card__sub">The currency used across your budgets.</p>
                    <div class="fields">
                        <div class="field">
                            <label class="label" for="fCurrency">Preferred currency</label>
                            <div class="control control--select">
                                <select id="fCurrency" name="currency">
                                    <?php foreach ($currencies as $code => $text): ?>
                                        <option value="<?php echo $code; ?>" <?php echo ($u['preferred_currency'] ?? 'USD') == $code ? 'selected' : ''; ?>><?php echo $text; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Save bar -->
                <footer class="actionbar" style="margin-top:0;">
                    <p class="err" style="margin:0 auto 0 0;"><?php echo htmlspecialchars($data['error']); ?></p>
                    <a class="btn btn--ghost" href="<?php echo URLROOT; ?>/customer/settings">Discard Changes</a>
                    <button class="btn btn--primary" type="submit">Save All Changes</button>
                </footer>
            </form>

        </div>

        <!-- ---------- Right column: password, two-factor, household ---------- -->
        <div class="col col--side">
            <!-- ===== Change password (separate form) ===== -->
            <section class="card" id="passwordCard">
                <h2 class="card__title">Change Password</h2>
                <p class="card__sub">Enter your current password, then choose a new one.</p>
                <form action="<?php echo URLROOT; ?>/customer/settings#passwordCard" method="post" class="fields">
                    <input type="hidden" name="form" value="password">
                    <div class="field">
                        <label class="label" for="pwCurrent">Current password</label>
                        <input class="control" id="pwCurrent" name="current" type="password" autocomplete="current-password" />
                    </div>
                    <div class="field">
                        <label class="label" for="pwNew">New password</label>
                        <input class="control" id="pwNew" name="new" type="password" autocomplete="new-password" />
                        <p class="hint">At least 8 characters.</p>
                    </div>
                    <div class="field">
                        <label class="label" for="pwConfirm">Confirm new password</label>
                        <input class="control" id="pwConfirm" name="confirm" type="password" autocomplete="new-password" />
                    </div>
                    <!-- button sits at the bottom of the row, level with the Confirm box -->
                    <div class="field" style="align-self:end;">
                        <button class="btn btn--primary" type="submit" style="width:100%; min-height:50px;">Update password</button>
                    </div>
                    <p class="err" style="grid-column:1 / -1; margin:0;"><?php echo htmlspecialchars($data['pwError']); ?></p>
                </form>
            </section>

            <!-- ===== Two-factor authentication (security PIN for Forgot Password) ===== -->
            <?php $tfaOn = !empty($u['two_factor_enabled']); ?>
            <section class="card" id="tfaCard">
                <div class="tfa__head">
                    <h2 class="card__title">Two-Factor Authentication</h2>
                    <span class="tfa__badge <?php echo $tfaOn ? 'is-on' : ''; ?>"><?php echo $tfaOn ? 'On' : 'Off'; ?></span>
                </div>
                <p class="card__sub">
                    <?php if ($tfaOn): ?>
                        Two-factor is <strong>on</strong>. Forgot Password asks for your <strong>email and your 6-digit security PIN</strong>.
                    <?php else: ?>
                        Two-factor is <strong>off</strong>. Forgot Password only asks for your <strong>email</strong>. Set a PIN to make it safer.
                    <?php endif; ?>
                </p>
                <form action="<?php echo URLROOT; ?>/customer/settings#tfaCard" method="post" class="fields">
                    <input type="hidden" name="form" value="<?php echo $tfaOn ? 'tfa_off' : 'tfa_on'; ?>">
                    <?php if (!$tfaOn): ?>
                        <div class="field">
                            <label class="label" for="tfaPin">Security PIN</label>
                            <input class="control" id="tfaPin" name="pin" type="password" inputmode="numeric" maxlength="6" placeholder="6 digits" autocomplete="off" />
                            <p class="hint">6 digits. Avoid easy ones like 123456 or 111111.</p>
                        </div>
                        <div class="field">
                            <label class="label" for="tfaPinConfirm">Confirm PIN</label>
                            <input class="control" id="tfaPinConfirm" name="pin_confirm" type="password" inputmode="numeric" maxlength="6" placeholder="6 digits" autocomplete="off" />
                        </div>
                    <?php endif; ?>
                    <div class="field">
                        <label class="label" for="tfaPassword">Current password to confirm</label>
                        <input class="control" id="tfaPassword" name="password" type="password" autocomplete="current-password" />
                    </div>
                    <div class="field" style="align-self:end;">
                        <?php if ($tfaOn): ?>
                            <button class="btn btn--danger" type="submit" style="width:100%; min-height:50px;">Turn off</button>
                        <?php else: ?>
                            <button class="btn btn--primary" type="submit" style="width:100%; min-height:50px;">Turn on</button>
                        <?php endif; ?>
                    </div>
                    <p class="err" style="grid-column:1 / -1; margin:0;"><?php echo htmlspecialchars($data['tfaError']); ?></p>
                </form>
            </section>

            <section class="card">
                <h2 class="card__title">Household</h2>
                <p class="card__sub">Everyone who can sign in to this account.</p>
                <ul class="people">
                    <?php foreach ($data['people'] as $person): ?>
                        <?php $pPhoto = avatarUrl($person['profile_picture_url']); ?>
                        <li class="person">
                            <span class="person__avatar" style="<?php echo $pPhoto ? "background-image:url('" . htmlspecialchars($pPhoto) . "')" : 'background-color:var(--color-primary)'; ?>"><?php echo $pPhoto ? '' : htmlspecialchars(strtoupper(substr($person['name'], 0, 1))); ?></span>
                            <span class="person__text">
                                <span class="person__name"><?php echo htmlspecialchars($person['name']); ?></span>
                                <span class="person__mail"><?php echo htmlspecialchars($person['email']); ?></span>
                            </span>
                            <?php if ($person['id'] == $_SESSION['user_id']): ?>
                                <span class="person__tag person__tag--you">You</span>
                            <?php elseif ($person['household_role'] == 'head'): ?>
                                <span class="person__tag person__tag--main">Holder</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a class="btn btn--soft btn--block" href="<?php echo URLROOT; ?>/customer/household">Manage household</a>
            </section>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/shared/footer.php'; ?>
