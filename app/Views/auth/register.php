<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">

        <h1 class="mt-5">Register</h1>

        <div id="response" class="alert d-none"></div>

        <form id="registerForm" class="mt-4">
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Confirm Password" required>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>

        <p class="mt-3">Already have an account? <a href="<?= site_url('auth/login') ?>">Login here</a></p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $('#registerForm').submit(function(e) {
            e.preventDefault();

            
            var csrfName = $('input[name=<?= csrf_token() ?>]').attr('name'); 
            var csrfHash = $('input[name=<?= csrf_token() ?>]').val(); 

            $.ajax({
                url: '<?= site_url('auth/register') ?>',
                type: 'POST',
                data: $(this).serialize() + '&' + csrfName + '=' + csrfHash,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.href = '<?= site_url('welcome') ?>';
                        $('#registerForm')[0].reset();
                    } else {
                        $('#response').removeClass('d-none alert-success').addClass('alert-danger').text(response.errors.join("\n"));
                    }
                    
                    $('input[name=<?= csrf_token() ?>]').val(response.csrfHash);
                },
                error: function() {
                    $('#response').removeClass('d-none alert-success').addClass('alert-danger').text('An error occurred. Please try again.');
                }
            });
        });
    </script>
</body>
</html>
