<!doctype html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link type="image/x-icon" rel="shortcut icon" href="./favicon.png"/>
  <link rel="stylesheet" href="./assets/import/element-plus.css"/>
  <link rel="stylesheet" href="./assets/css/layout.css"/>
  <link href="./assets/import/tailwind.min.css" rel="stylesheet">
  <script src="./assets/import/vue.js"></script>
  <script src="./assets/import/element-plus.js"></script>
  <title>Laravel HTML TPL</title>
  <link rel="stylesheet" href="./assets/icon/style.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    {!! include_content('style') !!}
  </style>
  <style>
    [v-cloak] {
      display: none;
    }
  </style>
</head>
<body>
<div id="app" v-cloak>
  <el-config-provider :button="button_config">
    <el-watermark :content="['', '']">
      <div>
        @include('components.header')
      </div>
      <div>
        @yield('content')
      </div>
      <div>
        @include('components.footer')
      </div>
    </el-watermark>
  </el-config-provider>
</div>
</body>
<script>
  const {createApp, onMounted, ref, nextTick, computed} = Vue
  const {ElLoading, ElMessage, ElMessageBox} = ElementPlus
</script>
<script src="./assets/mounting.js"></script>
<script>
  const App = {
    setup() {
      const button_config = {
        autoInsertSpace: true,
      }

      const onMountedAction = () => {
          {!! include_content('mounted') !!}
      }
      {!! include_content('script') !!}

      const pageOptions = (g) => {
        {!! include_content('options') !!}
      }

      const page_options = ref(pageOptions(@json($_GET)))
      const search_input = ref('')

      onMounted(() => {
        if (!!page_options.value && !!page_options.value.search) {
          search_input.value = page_options.value.search
        }
        onMountedAction()
      })

      return {
        search_input,
        button_config,
        page_options,
        {!! include_content('return') !!}
      }
    }
  }
  const app = createApp(App)
  app.use(ElementPlus)
  app.mount('#app')
</script>
</html>
