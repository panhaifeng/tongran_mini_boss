
  callbacks['passwdConfirm'] = (rule, value, callback) => {
    if (this.row.passwd != '' && value === '') {
      callback(new Error('请再次输入密码'));
    } else if (value != '' && value !== this.row.passwd) {
      callback(new Error('两次输入密码不一致!'));
    } else {
      callback();
    }
  };
