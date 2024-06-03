import pathlib
from typing import Optional, Dict, Tuple, List

from python_gettext_translations.translations import __

from tsr.config import TSR_LOCAL_LANGUAGE
from tsr.helpers.logger.app_logger import app_logger
from tsr.services.sketch.sketch_service import SketchService
from tsr.controllers.utils_controller import base_check_validation, get_current_directory

import typer


app = typer.Typer(no_args_is_help=True, help=__("Test %placeholder%", {"placeholder": "test"}))
