// 1) upload รูปก่อน
function uploadSignatureFile(selectedFile) {
  return new Promise((resolve, reject) => {
    const formData = new FormData();
    formData.append("file", selectedFile);

    $.ajax({
      url: "upload_handler_api.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (res) {
        if (res.status === "ok") {
          resolve(res); // คืน path + filename
        } else {
          reject(res.message);
        }
      },
      error: function (xhr) {
        reject("upload error");
      }
    });
  });
}

// 2) save user + path ของรูป
function saveUser(userData) {
  $.ajax({
    url: "user_handler_api.php",
    type: "POST",
    contentType: "application/json",
    dataType: "json",
    data: JSON.stringify({
      headerData: userData,
      action: "save"
    }),
    success: function (res) {
      console.log("Save user:", res);
    }
  });
}

// 3) Flow การทำงานรวม
$("#btnSave").on("click", async function () {
  try {
    const resUpload = await uploadSignatureFile(selectedFile);
    console.log("Upload result:", resUpload);

    const headerData = {
      user_id: userId,
      user_code: $("#user_code").val(),
      full_name: $("#full_name").val(),
      password: $("#password").val(),
      role_id: $("#role_id").val(),
      department_id: $("#department_id").val(),
      signature_path: resUpload.path // ใช้ path ที่ได้จาก upload_handler
    };

    saveUser(headerData);

  } catch (err) {
    alert("Error: " + err);
  }
});
