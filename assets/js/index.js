// Upload Status
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("applicant_image").addEventListener("change", function () {
        let statusText = this.files.length > 0 ? "Image successfully uploaded." : "Image not uploaded";
        let statusElement = document.getElementById("image-upload-status");
        statusElement.textContent = statusText;
        statusElement.style.color = this.files.length > 0 ? 'yellowgreen' : 'red';
    });

    document.getElementById("application_document").addEventListener("change", function () {
        let statusText = this.files.length > 0 ? "Document successfully uploaded." : "Document not uploaded";
        let statusElement = document.getElementById("document-upload-status");
        statusElement.textContent = statusText;
        statusElement.style.color = this.files.length > 0 ? 'yellowgreen' : 'red';
    });
});
