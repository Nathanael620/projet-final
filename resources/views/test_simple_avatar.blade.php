<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Simple Avatar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Test Simple Upload Avatar</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Sélectionner une image</label>
                            <input type="file" class="form-control" id="avatar" accept="image/*">
                        </div>
                        
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary" onclick="testUpload()">
                                Tester Upload
                            </button>
                        </div>

                        <div id="result" class="mt-3"></div>
                        
                        <div id="preview" class="mt-3 text-center" style="display: none;">
                            <h6>Aperçu :</h6>
                            <img id="preview-img" class="img-fluid rounded" style="max-width: 200px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Aperçu de l'image sélectionnée
        document.getElementById('avatar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('preview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // Test d'upload
        async function testUpload() {
            const fileInput = document.getElementById('avatar');
            const file = fileInput.files[0];
            const resultDiv = document.getElementById('result');

            if (!file) {
                resultDiv.innerHTML = '<div class="alert alert-warning">Veuillez sélectionner une image.</div>';
                return;
            }

            const formData = new FormData();
            formData.append('avatar', file);

            try {
                resultDiv.innerHTML = '<div class="alert alert-info">Upload en cours...</div>';

                const response = await fetch('/avatar/upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                const data = await response.json();
                console.log('Response data:', data);

                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-success">
                            <h6>✅ Succès !</h6>
                            <p>${data.message}</p>
                            <p><strong>URL de l'avatar :</strong> ${data.avatar_url}</p>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <h6>❌ Erreur !</h6>
                            <p>${data.message}</p>
                            ${data.errors ? '<ul>' + data.errors.map(error => '<li>' + error + '</li>').join('') + '</ul>' : ''}
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error:', error);
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h6>❌ Erreur de connexion !</h6>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        }
    </script>
</body>
</html> 