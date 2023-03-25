package com.accelerate.acceleronServices.user.dto.request;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import javax.validation.constraints.NotBlank;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Pattern;

@Data
@AllArgsConstructor
@NoArgsConstructor
@JsonIgnoreProperties(ignoreUnknown = true)
public class UserDto {

    @NotNull
    public int id;

    @NotBlank(message = "User Id cannot be empty")
    @Pattern(regexp = "^\\d{10}$", message = "Invalid Mobile Number")
    public String userId;

    @NotBlank(message = "Name cannot be empty")
    public String name;

    @NotBlank(message = "Email cannot be empty")
    public String email;

    @NotBlank(message = "Role cannot be empty")
    public Enum role;

    @NotNull(message = "Central Access cannot be empty")
    public boolean centralAccess;

    @NotBlank(message = "Password cannot be empty")
    public String password;

    @NotBlank(message = "Authentication Mode cannot be empty")
    public Enum authMode;

    @NotNull
    public boolean loginDisabled;

    public String lastLogin;

    @NotBlank(message = "Mode of Verification cannot be empty")
    public Enum modeOfVerification;

}
