<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Upload Example</title>
</head>
<body>
    <form id="form-register" method="POST" onsubmit="submitForm(event)">
        <table>
            <tr>
                <td><label for="name">Name: </label></td>
                <td>                
                    <input required type="text" name="name" id="name"/>
                </td>
            </tr>
            <tr>
                <td><label for="email">Email: </label></td>
                <td>                
                    <input required type="email" name="email" id="email"/>
                </td>
            </tr>
            <tr>
                <td><label for="password">Password: </label></td>
                <td>                
                    <input required type="password" name="password" id="password"/>
                </td>
            </tr>
            <tr>
                <td><label for="role">Role: </label></td>
                <td>                
                    <select name="role" id="role">
                        <option value="admin">Admin</option>
                        <option value="member">Member</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="avatar">Avatar: </label></td>
                <td>                
                    <input type="file" name="avatar" id="avatar"/>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><button>Submit</button></td>
            </tr>
        </table>
    </form>
    <script>
        function getFormData () {
            const form = document.getElementById('form-register');

            // Graphql Upload Form Spec Reference:
            // https://github.com/jaydenseric/graphql-multipart-request-spec
            const formData = new FormData;

            // Set operations: A JSON encoded operations object with files replaced with null.
            formData.set('operations', JSON.stringify({
                query: `
                    mutation($input: RegisterInput!) {
                        register(input: $input) {
                            id
                            name
                            email
                            password
                            role
                            avatar
                            created_at
                            updated_at
                            deleted_at
                        }
                    }
                `,
                variables: {
                    input: {
                        name: form.name.value,
                        email: form.email.value,
                        password: form.password.value,
                        role: form.role.value,
                        avatar: null
                    }
                }
            }));

            // Set map: A JSON encoded map of where files occurred in the operations.
            formData.set('map', JSON.stringify({
                "avatar": "variables.input.avatar"
            }));

            // Put file avatar
            formData.set('avatar', form.avatar.files[0]);

            return formData;
        }

        async function submitForm (e) {
            e.preventDefault();
            try {
                const formData = getFormData(e.target)
                const response = await fetch('index.php', {
                    method: 'POST',
                    body: formData
                });
                const json = await response.json();

                alert('Register success. \n ' + JSON.stringify(json));
                console.log('Register success: ', json);
            } catch (err) {
                alert('Register failed: ' + err.message + '\n\nOpen console for more detail.');
                console.error('Register failed: ', err);
            }
        }
    </script>
</body>
</html>