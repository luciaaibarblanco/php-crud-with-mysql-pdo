async function sendRequest(accion, formElement, infoDivElement) {
    try {
        const data = Object.fromEntries(new FormData(formElement));
        data.accion = accion;
        const response = await fetch("api.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams(data),
        });
        const respuesta = await response.json();
        infoDivElement.innerHTML = respuesta.texto;
    } catch (error) {
        infoDivElement.textContent = "ERROR inesperado";
    }
}

function onSubmit(accion, formId, infoDivId) {
    const formElement = document.getElementById(formId);
    const infoDivElement = document.getElementById(infoDivId);
    formElement.addEventListener("submit", async (e) => {
        e.preventDefault();
        await sendRequest(accion, formElement, infoDivElement);
    });
}

document
    .getElementById("deleteFormSubmitButton")
    .addEventListener("click", async (e) => {
        if (confirm("Eliminar ?")) {
            const formElement = document.getElementById("deleteForm");
            const infoDivElement = document.getElementById("deleteInfo");
            await sendRequest("delete", formElement, infoDivElement);
        }
    });

onSubmit("getAll", "getAllForm", "getAllInfo");
onSubmit("getById", "getByIdForm", "getByIdInfo");
onSubmit("add", "addForm", "addInfo");
onSubmit("update", "updateForm", "updateInfo");
