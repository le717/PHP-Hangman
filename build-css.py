import os
import distutils.file_util
import sass

os.chdir("css")

# Minified version
sass.compile(dirname=(".", "."), output_style="compressed")
distutils.file_util.copy_file("style.css", "style.min.css")

# Compiled version
sass.compile(dirname=(".", "."), output_style="expanded")
