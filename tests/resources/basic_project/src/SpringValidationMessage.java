import io.swagger.v3.oas.annotations.media.Schema;
import jakarta.validation.constraints.*;
import lombok.Getter;
import lombok.Setter;

@Getter
@Setter
public class SpringValidationMessage {
    @Schema(description = "User email")
    @Email
    @NotNull(message = "{\"Email\" cannot be blank}", min = "3")
    public String email;

    @NotNull(
                max = "3",
                message =
                        "{Password cannot be blank}"
    )
    public String password;
}
