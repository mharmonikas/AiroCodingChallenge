<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Insurance Quotation</title>
    <style>
        :root { color-scheme: light; font-family: Arial, sans-serif; color: #172033; background: #f4f7fb; }
        body { margin: 0; padding: 2rem 1rem; }
        main { max-width: 34rem; margin: 0 auto; background: #fff; border-radius: 0.75rem; padding: 2rem; box-shadow: 0 1px 8px #dce3ee; }
        h1 { margin-top: 0; font-size: 1.5rem; }
        p { color: #56647a; }
        label { display: block; font-weight: 600; margin-top: 1rem; }
        input, select, button { display: block; width: 100%; box-sizing: border-box; margin-top: 0.4rem; border-radius: 0.35rem; padding: 0.7rem; border: 1px solid #becadc; font: inherit; }
        button { margin-top: 1.5rem; color: #fff; background: #225bd6; border: 0; cursor: pointer; }
        button:disabled { opacity: 0.65; cursor: wait; }
        .message { margin-top: 1.25rem; border-radius: 0.35rem; padding: 0.8rem; display: none; }
        .hidden { display: none; }
        #login-errors, #quotation-errors { background: #fff0f0; color: #a22020; }
        #login-result, #quotation-result { background: #eafbF1; color: #14532d; }
    </style>
</head>
<body>
<main>
    <h1>Travel Insurance Quotation</h1>
    <p>Log in, then enter your trip details to receive a policy price.</p>

    <form id="login-form">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" autocomplete="email" required>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>

        <button type="submit">Log in</button>
    </form>

    <div id="login-errors" class="message" role="alert"></div>
    <div id="login-result" class="message" aria-live="polite"></div>

    <form id="quotation-form" class="hidden">
        <label for="age">Traveller ages</label>
        <input id="age" name="age" type="text" placeholder="28,35" required>

        <label for="currency_id">Currency</label>
        <select id="currency_id" name="currency_id" required>
            <option value="EUR">EUR</option>
            <option value="GBP">GBP</option>
            <option value="USD">USD</option>
        </select>

        <label for="start_date">Start date</label>
        <input id="start_date" name="start_date" type="date" required>

        <label for="end_date">End date</label>
        <input id="end_date" name="end_date" type="date" required>

        <button type="submit">Create quotation</button>
    </form>

    <div id="quotation-errors" class="message" role="alert"></div>
    <div id="quotation-result" class="message" aria-live="polite"></div>
</main>
<script>
    const loginForm = document.getElementById('login-form');
    const loginErrors = document.getElementById('login-errors');
    const loginResult = document.getElementById('login-result');
    const loginSubmit = loginForm.querySelector('button[type="submit"]');
    const quotationForm = document.getElementById('quotation-form');
    const quotationErrors = document.getElementById('quotation-errors');
    const quotationResult = document.getElementById('quotation-result');
    const quotationSubmit = quotationForm.querySelector('button[type="submit"]');

    function showCurrentForm() {
        const isLoggedIn = Boolean(localStorage.getItem('jwt_token'));

        loginForm.classList.toggle('hidden', isLoggedIn);
        quotationForm.classList.toggle('hidden', !isLoggedIn);
    }

    showCurrentForm();

    loginForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        loginErrors.style.display = 'none';
        loginResult.style.display = 'none';
        loginSubmit.disabled = true;

        const values = new FormData(loginForm);
        const request = {
            email: values.get('email'),
            password: values.get('password'),
        };

        try {
            const response = await fetch('/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(request),
            });
            const payload = await response.json();

            if (!response.ok) {
                const validationMessages = payload.errors
                    ? Object.values(payload.errors).flat().join(' ')
                    : payload.message || 'Unable to log in.';
                throw new Error(validationMessages);
            }

            localStorage.setItem('jwt_token', payload.token);
            loginResult.textContent = 'Logged in successfully.';
            loginResult.style.display = 'block';
            showCurrentForm();
        } catch (error) {
            loginErrors.textContent = error.message || 'The login request could not be completed.';
            loginErrors.style.display = 'block';
        } finally {
            loginSubmit.disabled = false;
        }
    });

    quotationForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        quotationErrors.style.display = 'none';
        quotationResult.style.display = 'none';
        quotationSubmit.disabled = true;

        const values = new FormData(quotationForm);
        const token = localStorage.getItem('jwt_token');
        const request = {
            age: values.get('age'),
            currency_id: values.get('currency_id'),
            start_date: values.get('start_date'),
            end_date: values.get('end_date'),
        };

        try {
            if (!token) {
                throw new Error('Please log in before requesting a quotation.');
            }

            const response = await fetch('/quotation', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(request),
            });
            const payload = await response.json();

            if (!response.ok) {
                if (response.status === 401) {
                    localStorage.removeItem('jwt_token');
                    showCurrentForm();
                }

                const validationMessages = payload.errors
                    ? Object.values(payload.errors).flat().join(' ')
                    : payload.message || 'Unable to create quotation.';
                throw new Error(validationMessages);
            }

            quotationResult.textContent = `Quotation #${payload.quotation_id}: ${payload.total.toFixed(2)} ${payload.currency_id}`;
            quotationResult.style.display = 'block';
        } catch (error) {
            quotationErrors.textContent = error.message || 'The request could not be completed.';
            quotationErrors.style.display = 'block';
        } finally {
            quotationSubmit.disabled = false;
        }
    });
</script>
</body>
</html>
