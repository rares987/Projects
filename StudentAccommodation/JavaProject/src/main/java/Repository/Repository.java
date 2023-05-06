package Repository;

import java.util.Optional;

public interface Repository<T> {

    int count();

    void deleteAll();

    void deleteById(int id);

    boolean existsById(int id);

    Iterable<T> findAll();

    T findById(int id);

    void save(T o);
}
