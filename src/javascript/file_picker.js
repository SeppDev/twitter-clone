async function openFilePicker() {
    const pickerOpts = {
        types: [
            {
                description: "Images",
                accept: {
                    "image/*": [".png", ".jpeg", ".jpg"],
                },
            },
        ],
        excludeAcceptAllOption: true,
        multiple: false,
    };
    const files = await window.showOpenFilePicker(pickerOpts);
    console.log(files[0]);
}