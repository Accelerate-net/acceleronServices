package com.accelerate.acceleronServices.user.service;

import com.accelerate.acceleronServices.user.dto.request.UserDto;
import com.accelerate.acceleronServices.user.dto.response.ApiResponse;
import com.accelerate.acceleronServices.user.dto.response.GenericResponse;
import com.accelerate.acceleronServices.user.model.UserEntity;

import java.util.List;

public interface UserService {

    // CRUD
    ApiResponse addUser(UserDto request);

    List<UserEntity> getAllUsers();

    UserEntity getUserById(int id);

    ApiResponse<GenericResponse> updateUserById(int id, UserDto request);

    ApiResponse deleteUserById(int id);
}
