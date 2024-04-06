{
    module: "EXT-Pages",
    position: "bottom_bar",
    config: {
      pages: {
        0: ["forecastWeather", "calend],
        1: [ "MMM-Alarm", "newsfeed", ]
      },
      fixed: [ "clock", "weather" ],
      hiddenPages: {
        "screenSaver": [ "clock", "MMM-SomeBackgroundImageModule" ],
        "admin": [ "MMM-ShowMeSystemStatsModule", "MMM-AnOnScreenMenuModule" ],
      },
      rotationTimes: {
        0: 20000
      },
      indicator: true,
      hideBeforeRotation: false,
      rotationTime: 15000,
      Gateway: {},
      loading: "loading.png"
    }
  },