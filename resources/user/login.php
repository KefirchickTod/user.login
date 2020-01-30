
<div id="back">
    <div class="backRight"></div>
    <div class="backLeft"></div>
</div>

<div id="slideBox">
    <div class="topLayer">
        <div class="left" >
            <div class="content">
                <h2>Sign Up</h2>
                <form method="post" action="/login/creat" >
                    <div class="form-group">
                        <input type="text" name="username" required placeholder="Username*" />
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" required placeholder="Email*">
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" required placeholder="Password*">
                    </div>
                    <div class="form-group"></div>
                    <button id="goLeft" class="off">Login</button>
                    <button type="submit" >Sign up</button>
                </form>
            </div>
        </div>
        <div class="right">
            <div class="content">
                <h2>Login</h2>
                <form method="post" action="/login">
                    <div class="form-group">
                        <input type="text" required placeholder="Username" />
                    </div>
                    <div class="form-group">
                        <input type="text" required placeholder="Password" />
                    </div>
                    <button onclick="return false;" id="goRight" class="off">Sign Up</button>
                    <button id="login" type="submit">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

