package com.accelerate.acceleronServices.user.utils;

import com.accelerate.acceleronServices.user.dto.request.UserDto;
import com.accelerate.acceleronServices.user.model.UserEntity;
import org.modelmapper.ModelMapper;
import org.modelmapper.TypeToken;
import org.springframework.beans.factory.annotation.Autowired;

import java.lang.reflect.Type;

public class UserEntityDtoConversion {

    @Autowired
    private ModelMapper modelMapper;

    public <T1, T2> T1 convert(T2 dto){
        Type typeOfT1 = new TypeToken<T1>(){}.getType();
        return modelMapper.map(dto, typeOfT1);
    }

    public <T> UserEntity convertToEntity(T entity){
        return modelMapper.map(entity, UserEntity.class);
    }

    public <T> UserDto convertToDto(T entity){ return modelMapper.map(entity, UserDto.class);}
}
