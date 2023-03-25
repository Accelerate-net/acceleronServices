package com.accelerate.acceleronServices.user.service;

import com.accelerate.acceleronServices.user.dto.request.UserDto;
import com.accelerate.acceleronServices.user.dto.response.ApiResponse;
import com.accelerate.acceleronServices.user.dto.response.GenericResponse;
import com.accelerate.acceleronServices.user.enums.StatusTextEnum;
import com.accelerate.acceleronServices.user.model.UserEntity;
import com.accelerate.acceleronServices.user.repository.UserRepository;
import com.accelerate.acceleronServices.user.utils.ResponseMessage;
import com.accelerate.acceleronServices.user.utils.UserEntityDtoConversion;
import lombok.AllArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Service;

import javax.persistence.EntityNotFoundException;
import java.util.List;

@Service
@AllArgsConstructor
@Slf4j
public class UserServiceImpl implements UserService{

    @Autowired
    private UserRepository userRepository;

    @Autowired
    private UserEntityDtoConversion userEntityDtoConversion;

    @Override
    public ApiResponse addUser(UserDto request) {
        UserEntity userEntity = userEntityDtoConversion.convertToEntity(request);
        userRepository.save(userEntity);

        ApiResponse<GenericResponse> response = new ApiResponse<>(true, HttpStatus.CREATED.value(), ResponseMessage.success,
                new GenericResponse(StatusTextEnum.SUCCESS.value()));
        return response;

    }

    @Override
    public List<UserEntity> getAllUsers() {
        return userRepository.findAll();
    }

    @Override
    public UserEntity getUserById(int id) {
        UserEntity userEntity = userRepository.getById(id);
        if (userEntity != null) {
            return userEntity;
        } else {
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }

    @Override
    public ApiResponse<GenericResponse> updateUserById(int id, UserDto request) {

        UserEntity requestEntity = userEntityDtoConversion.convertToEntity(request);
        UserEntity userEntity = userRepository.getById(id);

        if(userEntity != null){
            requestEntity.setId(userEntity.getId());
            userRepository.save(requestEntity);
            return new ApiResponse<>(true,HttpStatus.OK.value(),ResponseMessage.success,
                    new GenericResponse(StatusTextEnum.SUCCESS.value()));
        }
        else{ //if the id is not present
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }

    @Override
    public ApiResponse deleteUserById(int id) {
        if(userRepository.DeleteById(id) == 1){
            ApiResponse<GenericResponse> response = new ApiResponse<>(true, HttpStatus.OK.value(), ResponseMessage.success,
                    new GenericResponse(StatusTextEnum.SUCCESS.value()));
            return response;
        }
        else{
            throw new EntityNotFoundException(ResponseMessage.notFound);
        }
    }

}
