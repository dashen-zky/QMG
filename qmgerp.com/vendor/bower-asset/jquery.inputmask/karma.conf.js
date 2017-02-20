// Karma configuration
// Generated on Tue Mar 31 2015 23:46:42 GMT+0200 (CEST)

module.exports = function (config) {
	config.set({

		// base path that will be used to resolve all patterns (eg. files, exclude)
		basePath: '',


		// frameworks to use
		// available frameworks: https://npmjs.org/browse/keyword/karma-adapter
		frameworks: ['qunit'],


		// list of files / patterns to load in the browser
		files: [
			'node_modules/jquery/dist/jquery.js',
			//'js/**/*.js',
			'dist/jquery.inputmask.bundle.js',
			'qunit/prototypeExtensions.js',
			'qunit/simulator.js',
			'qunit/tests_*.js'
		],


		// list of files to exclude
		exclude: [],


		// preprocess matching files before serving them to the browser
		// available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
		preprocessors: {},


		// test results reporter to use
		// possible values: 'dots', 'progress'
		// available reporters: https://npmjs.org/browse/keyword/karma-reporter
		reporters: ['progress'],


		// web server port
		port: 9876,


		// enable / disable colors in the output (reporters and logs)
		colors: true,


		// level of logging
		// possible values: customer-config.LOG_DISABLE || customer-config.LOG_ERROR || customer-config.LOG_WARN || customer-config.LOG_INFO || customer-config.LOG_DEBUG
		logLevel: config.LOG_INFO,


		// enable / disable watching file and executing tests whenever any file changes
		autoWatch: true,


		// start these browsers
		// available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
		browsers: ["PhantomJS", "Chrome", "Firefox"],


		// Continuous Integration mode
		// if true, Karma captures browsers, runs the tests and exits
		singleRun: false
	})
}
