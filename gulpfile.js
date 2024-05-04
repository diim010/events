// Include gulp
const gulp = require('gulp');

// Include gulp-sass plugin
const sass = require('gulp-sass')(require('sass'));

// Compile SCSS files
gulp.task('sass', function () {
  return gulp.src('src/scss/**/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./css/'));
});

// Watch for changes in SCSS files
gulp.task('watch', function () {
  gulp.watch('src/scss/**/*.scss', gulp.series('sass'));
});

// Default task
gulp.task('default', gulp.series('sass', 'watch'));
