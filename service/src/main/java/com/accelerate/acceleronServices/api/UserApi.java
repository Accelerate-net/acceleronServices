package com.accelerate.acceleronServices.api;

import com.accelerate.acceleronServices.user.dto.request.UserDto;
import com.accelerate.acceleronServices.user.dto.response.ApiResponse;
import com.accelerate.acceleronServices.user.model.UserEntity;
import com.accelerate.acceleronServices.user.service.UserService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import javax.validation.Valid;
import java.util.List;


@RestController
@RequestMapping("/user")
public class UserApi {

    @Autowired
    private UserService userService;

    @PostMapping()
    public ResponseEntity<?> addUser(@RequestBody @Valid UserDto request) {

        ApiResponse response = userService.addUser(request);
        return new ResponseEntity<>(response, HttpStatus.CREATED);
    }

    @GetMapping
    public ResponseEntity<List<UserEntity>> getAllUsers(@RequestParam(required = false) Integer limit, @RequestParam(required = false) Integer skip){
        return new ResponseEntity<List<UserEntity>>(userService.getAllUsers(), HttpStatus.OK);
    }

    @GetMapping("/{id}")
    public ResponseEntity<UserEntity> getUserById(@PathVariable int id){
        return new ResponseEntity<UserEntity>(userService.getUserById(id), HttpStatus.OK);
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<?> deleteById(@PathVariable int id){
        ApiResponse response =  userService.deleteUserById(id);
        return ResponseEntity.ok().body(response);

    }
}
